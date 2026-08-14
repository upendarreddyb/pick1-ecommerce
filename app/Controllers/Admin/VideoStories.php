<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VideoTestimonialModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class VideoStories extends BaseController
{
    public function index()
    {
        return view('admin/testimonials/index', [
            'title' => 'Video stories',
            'rows'  => (new VideoTestimonialModel())->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    public function new()
    {
        return view('admin/testimonials/form', ['title' => 'Add video story']);
    }

    public function create()
    {
        return $this->save();
    }

    public function edit($id)
    {
        $row = (new VideoTestimonialModel())->find((int) $id);
        if (! $row) {
            throw PageNotFoundException::forPageNotFound('Video story not found.');
        }

        return view('admin/testimonials/form', ['title' => 'Edit video story', 'row' => $row]);
    }

    public function update($id)
    {
        return $this->save((int) $id);
    }

    private function save(?int $id = null)
    {
        $model = new VideoTestimonialModel();
        $old = $id ? $model->find($id) : null;
        if ($id && ! $old) {
            throw PageNotFoundException::forPageNotFound('Video story not found.');
        }

        $video = $this->request->getFile('video');
        $poster = $this->request->getFile('poster');
        $hasVideo = $video && $video->getError() !== UPLOAD_ERR_NO_FILE;
        $hasPoster = $poster && $poster->getError() !== UPLOAD_ERR_NO_FILE;
        $externalUrl = trim((string) $this->request->getPost('external_url'));
        $provider = $this->detectProvider($externalUrl);

        foreach ([['file' => $video, 'present' => $hasVideo, 'label' => 'Video'], ['file' => $poster, 'present' => $hasPoster, 'label' => 'Poster image']] as $upload) {
            if ($upload['present'] && $upload['file']->getError() !== UPLOAD_ERR_OK) {
                return redirect()->back()->withInput()->with('error', $upload['label'] . ' upload failed: ' . $upload['file']->getErrorString());
            }
        }

        $rules = [
            'customer_name' => 'required|max_length[120]',
            'title'         => 'permit_empty|max_length[180]',
            'review'        => 'permit_empty|max_length[2000]',
            'rating'        => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'sort_order'    => 'required|integer',
            'status'        => 'required|in_list[active,inactive]',
        ];
        if ((! $id && $externalUrl === '') || $hasVideo) {
            $rules['video'] = 'uploaded[video]|mime_in[video,video/mp4,video/webm,video/quicktime]|ext_in[video,mp4,webm,mov]|max_size[video,51200]';
        }
        if ($hasPoster) {
            $rules['poster'] = 'is_image[poster]|mime_in[poster,image/jpg,image/jpeg,image/png,image/webp]|ext_in[poster,jpg,jpeg,png,webp]|max_size[poster,5120]';
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }
        if ($externalUrl !== '' && $provider === null) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid YouTube or Instagram video link.');
        }
        if (! $id && ! $hasVideo && $externalUrl === '') {
            return redirect()->back()->withInput()->with('error', 'Upload a video or enter a YouTube/Instagram link.');
        }
        if ($id && ! $hasVideo && $externalUrl === '' && empty($old['video'])) {
            return redirect()->back()->withInput()->with('error', 'Keep the current link, upload a video, or enter a new YouTube/Instagram link.');
        }

        $data = [
            'customer_name' => trim((string) $this->request->getPost('customer_name')),
            'title'         => trim((string) $this->request->getPost('title')) ?: null,
            'review'        => trim((string) $this->request->getPost('review')) ?: null,
            'rating'        => (int) $this->request->getPost('rating'),
            'sort_order'    => (int) $this->request->getPost('sort_order'),
            'status'        => (string) $this->request->getPost('status'),
            'external_url'  => $externalUrl ?: null,
            'provider'      => $provider,
        ];

        $directory = FCPATH . 'uploads/video-stories';
        if (($hasVideo || $hasPoster) && ! is_dir($directory) && ! mkdir($directory, 0775, true)) {
            return redirect()->back()->withInput()->with('error', 'The video upload directory could not be created.');
        }

        $newFiles = [];
        try {
            if ($hasVideo) {
                $data['video'] = $video->getRandomName();
                $video->move($directory, $data['video']);
                $newFiles[] = $data['video'];
                $data['external_url'] = null;
                $data['provider'] = null;
            }
            if ($hasPoster) {
                $data['poster'] = $poster->getRandomName();
                $poster->move($directory, $data['poster']);
                $newFiles[] = $data['poster'];
            }
        } catch (\Throwable $exception) {
            foreach ($newFiles as $filename) {
                $this->deleteFile($filename);
            }
            log_message('error', 'Video story upload failed: {message}', ['message' => $exception->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'The uploaded file could not be saved. Check upload limits and folder permissions.');
        }

        $saved = $id ? $model->update($id, $data) : $model->insert($data);
        if ($saved === false) {
            foreach ($newFiles as $filename) {
                $this->deleteFile($filename);
            }
            return redirect()->back()->withInput()->with('error', implode(' ', $model->errors()) ?: 'The video story could not be saved.');
        }

        if ($old && $hasVideo) {
            if (! empty($old['video'])) {
                $this->deleteFile((string) $old['video']);
            }
        } elseif ($old && $externalUrl !== '' && ! empty($old['video'])) {
            $this->deleteFile((string) $old['video']);
            $data['video'] = null;
            $model->update($id, ['video' => null]);
        }
        if ($old && $hasPoster && ! empty($old['poster'])) {
            $this->deleteFile((string) $old['poster']);
        }

        return redirect()->to('/admin/video-stories')->with('message', 'Video story saved.');
    }

    public function delete($id)
    {
        $model = new VideoTestimonialModel();
        $row = $model->find((int) $id);
        if ($row) {
            $model->delete((int) $id);
            if (! empty($row['video'])) {
                $this->deleteFile((string) $row['video']);
            }
            if (! empty($row['poster'])) {
                $this->deleteFile((string) $row['poster']);
            }
        }

        return redirect()->to('/admin/video-stories')->with('message', 'Video story deleted.');
    }

    private function deleteFile(string $filename): void
    {
        $path = FCPATH . 'uploads/video-stories/' . basename($filename);
        if (is_file($path)) {
            unlink($path);
        }
    }

    private function detectProvider(string $url): ?string
    {
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host);
        if (in_array($host, ['youtube.com', 'm.youtube.com', 'youtu.be'], true)) {
            return 'youtube';
        }
        if (in_array($host, ['instagram.com'], true)) {
            return 'instagram';
        }

        return null;
    }
}
