<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;

class Products extends BaseController
{
    private function formData(?int $id = null): array
    {
        return [
            'title'      => $id ? 'Edit product' : 'New product',
            'row'        => $id ? (new ProductModel())->find($id) : null,
            'categories' => (new CategoryModel())->findAll(),
            'gallery'    => $id ? (new ProductImageModel())->where('product_id', $id)->orderBy('sort_order', 'ASC')->findAll() : [],
        ];
    }

    public function index()
    {
        return view('admin/products/index', [
            'title' => 'Products',
            'rows'  => (new ProductModel())->select('products.*,categories.name category')->join('categories', 'categories.id=products.category_id')->findAll(),
        ]);
    }

    public function new() { return view('admin/products/form', $this->formData()); }
    public function create() { return $this->save(); }
    public function edit($id) { return view('admin/products/form', $this->formData((int) $id)); }
    public function update($id) { return $this->save((int) $id); }

    private function save(?int $id = null)
    {
        $file = $this->request->getFile('image');
        $hasUpload = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $galleryFiles = array_values(array_filter(
            $this->request->getFileMultiple('gallery_images') ?? [],
            static fn ($galleryFile) => $galleryFile->getError() !== UPLOAD_ERR_NO_FILE
        ));
        $galleryModel = new ProductImageModel();
        $existingGallery = $id
            ? $galleryModel->where('product_id', $id)->orderBy('sort_order', 'ASC')->findAll()
            : [];
        $removeIds = array_map('intval', (array) $this->request->getPost('remove_gallery'));
        $remainingGallery = array_values(array_filter(
            $existingGallery,
            static fn (array $image): bool => ! in_array((int) $image['id'], $removeIds, true)
        ));

        // Do not silently save a product when PHP rejected the uploaded file.
        if ($hasUpload && $file->getError() !== UPLOAD_ERR_OK) {
            return redirect()->back()->withInput()->with('error', 'Image upload failed: ' . $file->getErrorString() . ' Use a JPG, PNG, or WebP image no larger than 2 MB.');
        }
        if (count($remainingGallery) + count($galleryFiles) > 4) {
            return redirect()->back()->withInput()->with('error', 'You can keep a maximum of four additional product images.');
        }
        foreach ($galleryFiles as $galleryFile) {
            if ($galleryFile->getError() !== UPLOAD_ERR_OK) {
                return redirect()->back()->withInput()->with('error', 'Additional image upload failed: ' . $galleryFile->getErrorString());
            }
            if ($galleryFile->getSizeByUnit('kb') > 2048
                || ! in_array($galleryFile->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)
                || ! in_array(strtolower($galleryFile->getExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return redirect()->back()->withInput()->with('error', 'Every additional image must be a JPG, PNG, or WebP file no larger than 2 MB.');
            }
        }

        $rules = [
            'name'        => 'required|max_length[180]',
            'category_id' => 'required|integer',
            'price'       => 'required|decimal',
            'stock'       => 'required|integer|greater_than_equal_to[0]',
        ];

        if ($hasUpload) {
            $rules['image'] = 'uploaded[image]|is_image[image]|mime_in[image,image/jpg,image/jpeg,image/png,image/webp]|ext_in[image,jpg,jpeg,png,webp]|max_size[image,2048]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $products = new ProductModel();
        $old = $id ? $products->find($id) : null;
        $data = [
            'name'        => trim((string) $this->request->getPost('name')),
            'slug'        => url_title((string) $this->request->getPost('name'), '-', true),
            'category_id' => (int) $this->request->getPost('category_id'),
            'description' => $this->request->getPost('description'),
            'price'       => $this->request->getPost('price'),
            'sale_price'  => $this->request->getPost('sale_price') ?: null,
            'stock'       => (int) $this->request->getPost('stock'),
            'status'      => $this->request->getPost('status') ?: 'inactive',
        ];

        $newImagePath = null;
        if ($hasUpload) {
            $uploadDirectory = FCPATH . 'uploads/products';
            if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0775, true)) {
                return redirect()->back()->withInput()->with('error', 'The product image directory could not be created.');
            }

            $filename = $file->getRandomName();
            try {
                $file->move($uploadDirectory, $filename);
            } catch (\Throwable $exception) {
                log_message('error', 'Product image move failed: {message}', ['message' => $exception->getMessage()]);
                return redirect()->back()->withInput()->with('error', 'The image could not be saved. Check the uploads directory permissions.');
            }
            $data['image'] = $filename;
            $newImagePath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
        }

        if ($galleryFiles && ! isset($uploadDirectory)) {
            $uploadDirectory = FCPATH . 'uploads/products';
            if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0775, true)) {
                return redirect()->back()->withInput()->with('error', 'The product image directory could not be created.');
            }
        }

        $saved = $id ? $products->update($id, $data) : $products->insert($data);
        if ($saved === false) {
            if ($newImagePath && is_file($newImagePath)) {
                unlink($newImagePath);
            }
            return redirect()->back()->withInput()->with('error', implode(' ', $products->errors()) ?: 'The product could not be saved.');
        }
        $productId = $id ?? (int) $products->getInsertID();

        if ($hasUpload && $old && ! empty($old['image'])) {
            $oldPath = FCPATH . 'uploads/products/' . basename($old['image']);
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        foreach ($existingGallery as $existingImage) {
            if (! in_array((int) $existingImage['id'], $removeIds, true)) {
                continue;
            }
            $galleryModel->delete((int) $existingImage['id']);
            $oldGalleryPath = FCPATH . 'uploads/products/' . basename($existingImage['image']);
            if (is_file($oldGalleryPath)) {
                unlink($oldGalleryPath);
            }
        }

        $nextSortOrder = count($remainingGallery);
        foreach ($galleryFiles as $galleryFile) {
            $galleryFilename = $galleryFile->getRandomName();
            try {
                $galleryFile->move($uploadDirectory, $galleryFilename);
                $galleryModel->insert([
                    'product_id' => $productId,
                    'image'      => $galleryFilename,
                    'sort_order' => $nextSortOrder++,
                ]);
            } catch (\Throwable $exception) {
                log_message('error', 'Product gallery image save failed: {message}', ['message' => $exception->getMessage()]);
                $galleryPath = $uploadDirectory . DIRECTORY_SEPARATOR . $galleryFilename;
                if (is_file($galleryPath)) {
                    unlink($galleryPath);
                }
                return redirect()->to('/admin/products/' . $productId . '/edit')->with('error', 'The product was saved, but one additional image could not be saved.');
            }
        }

        $imageMessage = ($hasUpload || $galleryFiles || $removeIds) ? ' Product gallery updated.' : '';
        return redirect()->to('/admin/products')->with('message', 'Product saved.' . $imageMessage);
    }

    public function delete($id)
    {
        $products = new ProductModel();
        $product = $products->find($id);
        if ($product && ! empty($product['image'])) {
            $path = FCPATH . 'uploads/products/' . basename($product['image']);
            if (is_file($path)) unlink($path);
        }
        $galleryModel = new ProductImageModel();
        foreach ($galleryModel->where('product_id', (int) $id)->findAll() as $galleryImage) {
            $path = FCPATH . 'uploads/products/' . basename($galleryImage['image']);
            if (is_file($path)) unlink($path);
        }
        $products->delete($id);
        return redirect()->to('/admin/products');
    }
}
