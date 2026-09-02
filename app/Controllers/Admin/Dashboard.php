<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $database = db_connect();
        $lastSeenOrderId = (int) session('admin_orders_seen_id');
        $unreadOrderCount = $database->table('orders')
            ->where('payment_status', 'paid')
            ->where('id >', $lastSeenOrderId)
            ->countAllResults();
        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'unreadOrderCount' => $unreadOrderCount,
            'stats' => [
                'orders' => $database->table('orders')->countAllResults(),
                'revenue' => $database->table('orders')->selectSum('total_amount')->where('payment_status', 'paid')->get()->getRow('total_amount') ?: 0,
                'pending' => $database->table('orders')->where('status', 'pending')->countAllResults(),
                'products' => $database->table('products')->countAllResults(),
            ],
        ]);
    }

    public function orderNotifications()
    {
        $database = db_connect();
        $lastSeenOrderId = (int) session('admin_orders_seen_id');
        $rows = $database->table('orders')
            ->select('orders.id, orders.total_amount, orders.status, orders.payment_status, orders.payment_method, orders.created_at, addresses.full_name, addresses.phone')
            ->join('addresses', 'addresses.id = orders.address_id', 'left')
            ->where('payment_status', 'paid')
            ->orderBy('orders.id', 'DESC')
            ->limit(30)
            ->get()
            ->getResultArray();

        $latestOrderId = $rows ? (int) $rows[0]['id'] : $lastSeenOrderId;
        session()->set('admin_orders_seen_id', max($lastSeenOrderId, $latestOrderId));

        return view('admin/order_notifications/index', [
            'title' => 'Order Notifications',
            'rows' => $rows,
            'lastSeenOrderId' => $lastSeenOrderId,
        ]);
    }
}
