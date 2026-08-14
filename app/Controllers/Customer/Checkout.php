<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Libraries\Cart;
use App\Libraries\PaymentGateway\RazorpayGateway;
use App\Models\AddressModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;

class Checkout extends BaseController
{
    public function index()
    {
        $cart = new Cart();
        if (! $cart->rows()) return redirect()->to('/cart');

        if ($this->request->getMethod() !== 'POST') {
            return view('customer/checkout/index', ['title' => 'Checkout', 'items' => $cart->rows(), 'total' => $cart->total()]);
        }

        $rules = [
            'full_name' => 'required|max_length[100]',
            'phone'     => 'required|min_length[10]',
            'line1'     => 'required',
            'city'      => 'required',
            'state'     => 'required',
            'pincode'   => 'required|numeric',
            'payment_method' => 'required|in_list[razorpay,gpay]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please complete all required address fields.');
        }

        $database = db_connect();
        $database->transBegin();

        try {
            $addresses = new AddressModel();
            $addressId = $addresses->insert([
                'user_id'   => session('customer_id'),
                'full_name' => $this->request->getPost('full_name'),
                'phone'     => $this->request->getPost('phone'),
                'line1'     => $this->request->getPost('line1'),
                'line2'     => $this->request->getPost('line2'),
                'city'      => $this->request->getPost('city'),
                'state'     => $this->request->getPost('state'),
                'pincode'   => $this->request->getPost('pincode'),
                'created_at'=> date('Y-m-d H:i:s'),
            ], true);

            $orders = new OrderModel();
            $orderId = $orders->insert([
                'user_id'     => session('customer_id'),
                'address_id'  => $addressId,
                'total_amount'=> $cart->total(),
                'payment_method' => $this->request->getPost('payment_method'),
            ], true);

            foreach ($cart->rows() as $row) {
                (new OrderItemModel())->insert([
                    'order_id'          => $orderId,
                    'product_id'        => $row['product_id'],
                    'product_name'      => $row['name'],
                    'quantity'          => $row['quantity'],
                    'price_at_purchase' => $row['sale_price'] ?: $row['price'],
                ]);
            }

            $gatewayOrder = (new RazorpayGateway())->createOrder($cart->total(), 'order_' . $orderId);
            $orders->update($orderId, ['gateway_order_id' => $gatewayOrder['id']]);
            (new PaymentModel())->insert([
                'order_id'    => $orderId,
                'gateway'     => 'razorpay',
                'amount'      => $cart->total(),
                'status'      => 'created',
                'raw_response'=> json_encode($gatewayOrder),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            if ($database->transStatus() === false) {
                throw new \RuntimeException('The order records could not be created.');
            }
            $database->transCommit();
        } catch (\Throwable $exception) {
            $database->transRollback();
            log_message('error', 'Checkout creation failed: {message}', ['message' => $exception->getMessage()]);
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return view('customer/checkout/pay', [
            'title'   => 'Complete payment',
            'order'   => $orders->find($orderId),
            'gateway' => $gatewayOrder,
            'key'     => env('RAZORPAY_KEY_ID'),
            'items'   => $cart->rows(),
            'paymentMethod' => $this->request->getPost('payment_method'),
        ]);
    }

    public function verifyPayment()
    {
        $payload = $this->request->getPost();
        $order = (new OrderModel())->where(['id' => $payload['order_id'] ?? 0, 'user_id' => session('customer_id')])->first();
        if (! $order || ! (new RazorpayGateway())->verify($payload)) {
            return redirect()->to('/orders')->with('error', 'Payment verification failed. You can retry safely.');
        }

        (new OrderModel())->update($order['id'], ['payment_status' => 'paid', 'status' => 'processing']);
        (new PaymentModel())->where('order_id', $order['id'])->set([
            'transaction_id' => $payload['razorpay_payment_id'],
            'status'         => 'paid',
            'raw_response'   => json_encode($payload),
        ])->update();
        (new Cart())->clear();

        return redirect()->to('/orders/' . $order['id'])->with('message', 'Payment received. Thank you!');
    }
}
