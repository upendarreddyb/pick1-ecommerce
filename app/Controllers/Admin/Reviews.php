<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductReviewModel;

class Reviews extends BaseController
{
    public function index()
    {
        $reviews = new ProductReviewModel();
        $status = (string) $this->request->getGet('status');
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $reviews->where('product_reviews.status', $status);
        }

        return view('admin/reviews/index', [
            'title' => 'Reviews',
            'rows' => $reviews
                ->select('product_reviews.*, products.name AS product_name, users.email AS customer_email')
                ->join('products', 'products.id = product_reviews.product_id')
                ->join('users', 'users.id = product_reviews.user_id')
                ->orderBy('product_reviews.id', 'DESC')
                ->findAll(),
            'status' => $status,
        ]);
    }

    public function status(int $id)
    {
        $status = (string) $this->request->getPost('status');
        if (! in_array($status, ['approved', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Invalid review status.');
        }

        (new ProductReviewModel())->update($id, ['status' => $status]);
        return redirect()->back()->with('message', 'Review ' . $status . '.');
    }

    public function delete(int $id)
    {
        (new ProductReviewModel())->delete($id);
        return redirect()->back()->with('message', 'Review deleted.');
    }
}
