<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SliderModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Sliders extends BaseController
{
    public function index()
    {
        return view('admin/sliders/index', [
            'title' => 'Homepage slider',
            'rows'  => (new SliderModel())->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    public function new()
    {
        return view('admin/sliders/form', ['title' => 'Add slide']);
    }

    public function create()
    {
        return $this->save();
    }

    public function edit($id)
    {
        $row = (new SliderModel())->find((int) $id);
        if (! $row) {
            throw PageNotFoundException::forPageNotFound('Slider not found.');
        }

        return view('admin/sliders/form', ['title' => 'Edit slide', 'row' => $row]);
    }

    public function update($id)
    {
        return $this->save((int) $id);
    }

    private function save(?int $id = null)
    {
        $sliders = new SliderModel();
        $old     = $id ? $sliders->find($id) : null;
        if ($id && ! $old) {
            throw PageNotFoundException::forPageNotFound('Slider not found.');
        }

        $file      = $this->request->getFile('image');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;

        if ($hasUpload && $file->getError() !== UPLOAD_ERR_OK) {
            return redirect()->back()->withInput()->with('error', 'Slider image upload failed: ' . $file->getErrorString());
        }

        $rules = [
            'title'       => 'required|max_length[180]',
            'eyebrow'     => 'permit_empty|max_length[120]',
            'description' => 'permit_empty|max_length[300]',
            'button_text' => 'permit_empty|max_length[80]',
            'button_url'  => 'permit_empty|max_length[255]',
            'sort_order'  => 'required|integer',
            'status'      => 'required|in_list[active,inactive]',
        ];
        if (! $id || $hasUpload) {
            $rules['image'] = 'uploaded[image]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|ext_in[image,jpg,jpeg,png,webp]|max_size[image,5120]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $buttonUrl = trim((string) $this->request->getPost('button_url'));
        if ($buttonUrl !== '' && preg_match('/^[a-z][a-z0-9+.-]*:/i', $buttonUrl) && ! preg_match('#^https?://#i', $buttonUrl)) {
            return redirect()->back()->withInput()->with('error', 'Button URL must be a website path or an HTTP/HTTPS URL.');
        }

        $data = [
            'eyebrow'     => trim((string) $this->request->getPost('eyebrow')) ?: null,
            'title'       => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'button_text' => trim((string) $this->request->getPost('button_text')) ?: null,
            'button_url'  => $buttonUrl ?: null,
            'sort_order'  => (int) $this->request->getPost('sort_order'),
            'status'      => (string) $this->request->getPost('status'),
        ];

        $newImagePath = null;
        if ($hasUpload) {
            $directory = FCPATH . 'uploads/sliders';
            if (! is_dir($directory) && ! mkdir($directory, 0775, true)) {
                return redirect()->back()->withInput()->with('error', 'The slider upload directory could not be created.');
            }

            $filename = $file->getRandomName();
            try {
                $file->move($directory, $filename);
            } catch (\Throwable $exception) {
                log_message('error', 'Slider image move failed: {message}', ['message' => $exception->getMessage()]);
                return redirect()->back()->withInput()->with('error', 'The slider image could not be saved. Check folder permissions.');
            }
            $data['image'] = $filename;
            $newImagePath  = $directory . DIRECTORY_SEPARATOR . $filename;
        }

        $saved = $id ? $sliders->update($id, $data) : $sliders->insert($data);
        if ($saved === false) {
            if ($newImagePath && is_file($newImagePath)) {
                unlink($newImagePath);
            }
            return redirect()->back()->withInput()->with('error', implode(' ', $sliders->errors()) ?: 'The slide could not be saved.');
        }

        if ($hasUpload && $old && ! empty($old['image'])) {
            $this->deleteImage((string) $old['image']);
        }

        return redirect()->to('/admin/sliders')->with('message', 'Homepage slide saved.');
    }

    public function delete($id)
    {
        $sliders = new SliderModel();
        $row     = $sliders->find((int) $id);
        if ($row) {
            $sliders->delete((int) $id);
            $this->deleteImage((string) $row['image']);
        }

        return redirect()->to('/admin/sliders')->with('message', 'Homepage slide deleted.');
    }

    private function deleteImage(string $filename): void
    {
        $path = FCPATH . 'uploads/sliders/' . basename($filename);
        if (is_file($path)) {
            unlink($path);
        }
    }
}
