<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CouponModel;

class Coupons extends BaseController
{
    public function index()
    {
        $this->ensureTable();
        return view('admin/coupons/index', ['title' => 'Coupons', 'rows' => (new CouponModel())->orderBy('id', 'DESC')->findAll()]);
    }

    public function create()
    {
        $this->ensureTable();
        $code = strtoupper(trim((string) $this->request->getPost('code')));
        if (! preg_match('/^[A-Z0-9_-]{3,50}$/', $code)) return redirect()->back()->withInput()->with('error', 'Use 3–50 letters, numbers, hyphens, or underscores.');
        $model = new CouponModel();
        if ($model->where('code', $code)->first()) return redirect()->back()->withInput()->with('error', 'That coupon code already exists.');
        $model->insert(['code' => $code, 'status' => 'active']);
        return redirect()->to('/admin/coupons')->with('message', '10% coupon added.');
    }

    public function status($id)
    {
        $this->ensureTable();
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['active', 'inactive'], true)) return redirect()->back()->with('error', 'Invalid coupon status.');
        (new CouponModel())->update((int) $id, ['status' => $status]);
        return redirect()->to('/admin/coupons')->with('message', 'Coupon status updated.');
    }

    public function delete($id)
    {
        $this->ensureTable();
        (new CouponModel())->delete((int) $id);
        return redirect()->to('/admin/coupons')->with('message', 'Coupon deleted.');
    }

    private function ensureTable(): void
    {
        db_connect()->query("CREATE TABLE IF NOT EXISTS coupons (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, code VARCHAR(50) NOT NULL UNIQUE, status ENUM('active','inactive') NOT NULL DEFAULT 'active', created_at DATETIME NULL, updated_at DATETIME NULL) ENGINE=InnoDB");
    }
}
