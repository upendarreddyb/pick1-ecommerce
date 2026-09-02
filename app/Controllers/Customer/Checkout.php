<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Libraries\Cart;
use App\Libraries\PaymentGateway\RazorpayGateway;
use App\Libraries\WhatsAppCloudApi;
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
            'payment_method' => 'required|in_list[razorpay]',
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
            'key'     => env('RAZORPAY_KEY_ID'),
            'items'   => $cart->rows(),
            'paymentMethod' => $this->request->getPost('payment_method'),
            'prefill' => [
                'name'    => (string) $this->request->getPost('full_name'),
                'email'   => (string) session('customer_email'),
                'contact' => (string) $this->request->getPost('phone'),
            ],
        ]);
    }

    public function createOrder()
    {
        $input = $this->requestPayload();
        $orderId = (int) ($input['order_id'] ?? 0);
        $orders = new OrderModel();
        $order = $orders->where([
            'id'      => $orderId,
            'user_id' => session('customer_id'),
        ])->first();

        if (! $order) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Order not found.',
            ]);
        }

        if (($order['payment_status'] ?? '') === 'paid') {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'This order has already been paid.',
            ]);
        }

        $amountInPaise = (int) round(((float) $order['total_amount']) * 100);
        if ($amountInPaise < 100) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'The minimum payment amount is ₹1.00.',
            ]);
        }

        try {
            if (! empty($order['gateway_order_id'])) {
                $gatewayOrder = [
                    'id'       => $order['gateway_order_id'],
                    'amount'   => $amountInPaise,
                    'currency' => 'INR',
                ];

                $payments = new PaymentModel();
                if (! $payments->where('order_id', $order['id'])->first()) {
                    $payments->insert([
                        'order_id'    => $order['id'],
                        'gateway'     => 'razorpay',
                        'amount'      => $order['total_amount'],
                        'status'      => 'created',
                        'created_at'  => date('Y-m-d H:i:s'),
                    ]);
                }
            } else {
                $gatewayOrder = (new RazorpayGateway())->createOrder(
                    (float) $order['total_amount'],
                    order_number($order),
                );

                $orders->update($order['id'], ['gateway_order_id' => $gatewayOrder['id']]);

                $payments = new PaymentModel();
                $existingPayment = $payments->where('order_id', $order['id'])->first();
                $paymentData = [
                    'gateway'      => 'razorpay',
                    'amount'       => $order['total_amount'],
                    'status'       => 'created',
                    'raw_response' => json_encode($gatewayOrder),
                ];

                if ($existingPayment) {
                    $payments->update($existingPayment['id'], $paymentData);
                } else {
                    $payments->insert($paymentData + [
                        'order_id'   => $order['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        } catch (\RuntimeException $exception) {
            log_message('error', 'Razorpay order creation failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            $status = $exception->getCode() === 401 ? 401 : 500;
            return $this->response->setStatusCode($status)->setJSON([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        } catch (\Throwable $exception) {
            log_message('error', 'Checkout API failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'The payment order could not be created. Please try again.',
            ]);
        }

        return $this->response->setJSON([
            'success'   => true,
            'order_id'  => $gatewayOrder['id'],
            'amount'    => (int) $gatewayOrder['amount'],
            'currency'  => $gatewayOrder['currency'] ?? 'INR',
            'csrf_name' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }

    public function verifyPayment()
    {
        $payload = $this->requestPayload();
        foreach (['order_id', 'razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'] as $field) {
            if (empty($payload[$field])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Missing payment verification fields.',
                ]);
            }
        }

        $orders = new OrderModel();
        $order = $orders->where([
            'id'      => (int) $payload['order_id'],
            'user_id' => session('customer_id'),
        ])->first();

        if (! $order || empty($order['gateway_order_id'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'The payment order could not be verified.',
            ]);
        }

        if (($order['payment_status'] ?? '') === 'paid') {
            return $this->response->setJSON([
                'success'  => true,
                'redirect' => base_url('orders/' . $order['id']),
            ]);
        }

        if (! (new RazorpayGateway())->verify($payload, (string) $order['gateway_order_id'])) {
            log_message('warning', 'Rejected Razorpay signature for order {orderId}.', [
                'orderId' => $order['id'],
            ]);

            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Payment signature verification failed.',
            ]);
        }

        $database = db_connect();
        $database->transBegin();

        try {
            $orders->update($order['id'], [
                'payment_status' => 'paid',
                'status'         => 'processing',
            ]);
            (new PaymentModel())->where('order_id', $order['id'])->set([
                'transaction_id' => $payload['razorpay_payment_id'],
                'status'         => 'paid',
                'raw_response'   => json_encode([
                    'razorpay_order_id'   => $payload['razorpay_order_id'],
                    'razorpay_payment_id' => $payload['razorpay_payment_id'],
                    'razorpay_signature'  => $payload['razorpay_signature'],
                ]),
            ])->update();

            if ($database->transStatus() === false) {
                throw new \RuntimeException('The verified payment could not be saved.');
            }

            $database->transCommit();
        } catch (\Throwable $exception) {
            $database->transRollback();
            log_message('error', 'Verified payment persistence failed: {message}', [
                'message' => $exception->getMessage(),
            ]);

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Payment was verified but could not be saved. Please contact support.',
            ]);
        }

        // Payment success must not depend on the external messaging service.
        // Any WhatsApp failure is logged by the client and the checkout proceeds.
        $confirmedOrder = $orders->find($order['id']);
        $deliveryAddress = (new AddressModel())->find((int) $order['address_id']);
        if ($confirmedOrder && $deliveryAddress) {
            (new WhatsAppCloudApi())->sendOrderConfirmation($confirmedOrder, $deliveryAddress);
        }

        (new Cart())->clear();

        return $this->response->setJSON([
            'success'  => true,
            'redirect' => base_url('orders/' . $order['id']),
        ]);
    }

    private function requestPayload(): array
    {
        if (str_contains(strtolower($this->request->getHeaderLine('Content-Type')), 'application/json')) {
            $json = $this->request->getJSON(true);
            return is_array($json) ? $json : [];
        }

        return $this->request->getPost();
    }
}
