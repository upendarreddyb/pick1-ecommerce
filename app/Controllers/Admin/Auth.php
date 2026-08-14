<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class Auth extends BaseController
{
    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $admin = (new AdminModel())
                ->where('email', strtolower(trim((string) $this->request->getPost('email'))))
                ->first();

            if ($admin && password_verify((string) $this->request->getPost('password'), $admin['password'])) {
                session()->set(['admin_id' => $admin['id'], 'admin_name' => $admin['name']]);
                return redirect()->to('/admin');
            }

            return redirect()->back()->with('error', 'Invalid email or password.');
        }

        return view('admin/auth/login', ['title' => 'Admin sign in']);
    }

    public function password()
    {
        if ($this->request->getMethod() !== 'POST') {
            return view('admin/auth/password', ['title' => 'Change password']);
        }

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[10]|max_length[72]',
            'confirm_password' => 'required|matches[new_password]',
        ];
        $messages = [
            'new_password' => [
                'min_length' => 'The new password must contain at least 10 characters.',
                'max_length' => 'The new password cannot exceed 72 characters.',
            ],
            'confirm_password' => ['matches' => 'The password confirmation does not match.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', $this->validator->getError());
        }

        $adminModel = new AdminModel();
        $admin      = $adminModel->find((int) session('admin_id'));

        if (! $admin || ! password_verify((string) $this->request->getPost('current_password'), $admin['password'])) {
            return redirect()->back()->with('error', 'The current password is incorrect.');
        }

        $newPassword = (string) $this->request->getPost('new_password');
        if (password_verify($newPassword, $admin['password'])) {
            return redirect()->back()->with('error', 'Choose a password different from your current password.');
        }

        if (! $adminModel->update($admin['id'], ['password' => password_hash($newPassword, PASSWORD_DEFAULT)])) {
            return redirect()->back()->with('error', 'The password could not be updated. Please try again.');
        }

        session()->regenerate(true);
        return redirect()->to('/admin/password')->with('message', 'Your admin password has been updated.');
    }

    public function logout()
    {
        session()->remove(['admin_id', 'admin_name']);
        return redirect()->to('/admin/login');
    }
}
