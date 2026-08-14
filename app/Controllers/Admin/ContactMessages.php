<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactMessageModel;

class ContactMessages extends BaseController
{
    public function index()
    {
        return view('admin/contact_messages', ['title' => 'Contact messages', 'rows' => (new ContactMessageModel())->orderBy('id', 'DESC')->findAll()]);
    }

    public function status($id)
    {
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['new', 'read', 'resolved'], true)) {
            return redirect()->back()->with('error', 'Invalid message status.');
        }
        (new ContactMessageModel())->update((int) $id, ['status' => $status]);
        return redirect()->back()->with('message', 'Message status updated.');
    }
}
