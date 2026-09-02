<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderItemModel;
use App\Models\OrderModel;

class Orders extends BaseController
{
    private const STATUSES = ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled'];

    public function index()
    {
        $model = new OrderModel();
        $status = (string) $this->request->getGet('status');
        if (in_array($status, self::STATUSES, true)) {
            $model->where('status', $status);
        }

        return view('admin/orders/index', [
            'title' => 'Orders',
            'rows' => $model->select('orders.*,users.email')->join('users', 'users.id=orders.user_id')->orderBy('id', 'DESC')->findAll(),
        ]);
    }

    public function show($id)
    {
        return view('admin/orders/show', [
            'title' => 'Order ' . order_number((int) $id),
            'row' => (new OrderModel())->find($id),
            'items' => (new OrderItemModel())->where('order_id', $id)->findAll(),
        ]);
    }

    public function status($id)
    {
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, self::STATUSES, true)) {
            return redirect()->back()->with('error', 'Choose a valid order status.');
        }

        if ($status === 'out_for_delivery') {
            $this->ensureOutForDeliveryStatusIsAvailable();
        }

        (new OrderModel())->update($id, ['status' => $status]);
        return redirect()->back()->with('message', 'Order status updated.');
    }

    private function ensureOutForDeliveryStatusIsAvailable(): void
    {
        $database = db_connect();
        $column = $database->query("SHOW COLUMNS FROM orders LIKE 'status'")->getRowArray();
        if (! str_contains((string) ($column['Type'] ?? ''), 'out_for_delivery')) {
            $database->query("ALTER TABLE orders MODIFY status ENUM('pending','processing','shipped','out_for_delivery','delivered','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
}
