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
        $recentOrders = $database->table('orders')
            ->select('orders.id, orders.total_amount, orders.status, orders.payment_status, orders.created_at, addresses.full_name')
            ->join('addresses', 'addresses.id = orders.address_id', 'left')
            ->where('orders.payment_status', 'paid')
            ->where('orders.id >', $lastSeenOrderId)
            ->orderBy('orders.id', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return view('admin/dashboard', [
            'title' => 'Dashboard',
            'recentOrders' => $recentOrders,
            'unreadOrderCount' => $unreadOrderCount,
            'latestOrderId' => $recentOrders ? (int) $recentOrders[0]['id'] : $lastSeenOrderId,
            'stats' => [
                'orders' => $database->table('orders')->countAllResults(),
                'revenue' => $database->table('orders')->selectSum('total_amount')->where('payment_status', 'paid')->get()->getRow('total_amount') ?: 0,
                'pending' => $database->table('orders')->where('status', 'pending')->countAllResults(),
                'products' => $database->table('products')->countAllResults(),
            ],
        ]);
    }

    public function readOrderNotifications()
    {
        $latestOrder = db_connect()->table('orders')
            ->selectMax('id')
            ->where('payment_status', 'paid')
            ->get()
            ->getRowArray();

        session()->set('admin_orders_seen_id', (int) ($latestOrder['id'] ?? 0));

        return $this->response->setJSON(['success' => true]);
    }
}
