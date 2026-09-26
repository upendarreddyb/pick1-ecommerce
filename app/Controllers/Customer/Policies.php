<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\StoreSettingModel;

class Policies extends BaseController
{
    public function index()
    {
        return view('customer/policies/index', ['title' => 'Website Policies', 'pricing' => StoreSettingModel::values()]);
    }

    public function disclaimer()
    {
        return view('customer/policies/disclaimer', ['title' => 'Disclaimer']);
    }
}
