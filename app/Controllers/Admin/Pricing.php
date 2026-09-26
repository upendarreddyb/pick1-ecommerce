<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StoreSettingModel;

class Pricing extends BaseController
{
    public function index()
    {
        StoreSettingModel::ensureTable();
        return view('admin/pricing', [
            'title' => 'Shipping & GST',
            'settings' => StoreSettingModel::values(),
        ]);
    }

    public function save()
    {
        $rules = [
            'shipping_charge' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100000]',
            'free_shipping_minimum' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[1000000]',
            'gst_rate' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        StoreSettingModel::ensureTable();
        (new StoreSettingModel())->saveValues([
            'shipping_charge' => $this->request->getPost('shipping_charge'),
            'free_shipping_minimum' => $this->request->getPost('free_shipping_minimum'),
            'gst_rate' => $this->request->getPost('gst_rate'),
        ]);

        return redirect()->to('/admin/pricing')->with('message', 'Shipping and GST settings updated.');
    }
}
