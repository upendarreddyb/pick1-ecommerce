<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\AddressModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Orders extends BaseController
{
    public function index()
    {
        return view('customer/orders/index', [
            'title'  => 'Your orders',
            'orders' => (new OrderModel())
                ->select('id, total_amount, status, created_at')
                ->where('user_id', session('customer_id'))
                ->orderBy('id', 'DESC')
                ->findAll(30),
        ]);
    }

    public function show($id)
    {
        $order = (new OrderModel())->where([
            'id'      => (int) $id,
            'user_id' => session('customer_id'),
        ])->first();

        if (! $order) {
            throw PageNotFoundException::forPageNotFound();
        }

        $items = (new OrderItemModel())
            ->select('order_items.*, products.image AS product_image, products.slug AS product_slug')
            ->join('products', 'products.id = order_items.product_id', 'left')
            ->where('order_items.order_id', (int) $id)
            ->findAll();

        $address = (new AddressModel())->where([
            'id'      => (int) $order['address_id'],
            'user_id' => session('customer_id'),
        ])->first();

        return view('customer/orders/show', [
            'title'   => 'Order ' . order_number($order),
            'order'   => $order,
            'items'   => $items,
            'address' => $address,
        ]);
    }
}
