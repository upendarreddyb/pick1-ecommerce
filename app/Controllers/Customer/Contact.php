<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\ContactMessageModel;

class Contact extends BaseController
{
    public function index()
    {
        return view('customer/contact', ['title' => 'Contact us']);
    }

    public function create()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[190]',
            'message' => 'required|min_length[5]|max_length[2000]',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }
        $saved = (new ContactMessageModel())->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'email' => strtolower(trim((string) $this->request->getPost('email'))),
            'message' => trim((string) $this->request->getPost('message')),
            'status' => 'new',
        ]);
        return $saved === false
            ? redirect()->back()->withInput()->with('error', 'Your message could not be saved. Please try again.')
            : redirect()->to('/contact')->with('message', 'Thank you. Your message has been received.');
    }
}
