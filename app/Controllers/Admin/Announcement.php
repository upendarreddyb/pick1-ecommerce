<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcement extends BaseController
{
    public function index()
    {
        $model = new AnnouncementModel();
        return view('admin/announcement', ['title' => 'Header discount', 'row' => $model->first()]);
    }

    public function save()
    {
        if (! $this->validate(['message' => 'required|max_length[220]', 'status' => 'required|in_list[active,inactive]', 'speed' => 'required|integer|greater_than_equal_to[8]|less_than_equal_to[60]'])) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }
        $model = new AnnouncementModel();
        $row = $model->first();
        $data = ['message' => trim((string) $this->request->getPost('message')), 'status' => (string) $this->request->getPost('status'), 'speed' => (int) $this->request->getPost('speed')];
        $saved = $row ? $model->update((int) $row['id'], $data) : $model->insert($data);
        return $saved === false
            ? redirect()->back()->withInput()->with('error', 'The header discount could not be saved.')
            : redirect()->to('/admin/announcement')->with('message', 'Header discount updated.');
    }
}
