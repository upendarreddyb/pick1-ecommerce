<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

class Policies extends BaseController
{
    public function index()
    {
        return view('customer/policies/index', ['title' => 'Website Policies']);
    }

    public function disclaimer()
    {
        return view('customer/policies/disclaimer', ['title' => 'Disclaimer']);
    }
}
