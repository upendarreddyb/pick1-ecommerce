<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\ProductReviewModel;

class Reviews extends BaseController
{
    public function create(int $productId)
    {
        $product = (new ProductModel())->find($productId);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate([
            'rating' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'review' => 'required|min_length[5]|max_length[1000]',
        ])) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userId = (int) session('customer_id');
        $purchased = db_connect()->table('order_items')
            ->select('order_items.id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('orders.user_id', $userId)
            ->where('order_items.product_id', $productId)
            ->where('orders.payment_status', 'paid')
            ->where('orders.status', 'delivered')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (! $purchased) {
            return redirect()->back()->with('error', 'Only customers with a delivered order for this product can submit a review.');
        }

        $reviews = new ProductReviewModel();
        $existing = $reviews->where(['product_id' => $productId, 'user_id' => $userId])->first();
        $data = [
            'product_id'       => $productId,
            'user_id'          => $userId,
            'rating'           => (int) $this->request->getPost('rating'),
            'review'           => trim((string) $this->request->getPost('review')),
            'status'           => 'pending',
            'verified_purchase'=> 1,
        ];

        $saved = $existing
            ? $reviews->update((int) $existing['id'], $data)
            : $reviews->insert($data);

        if ($saved === false) {
            return redirect()->back()->withInput()->with('error', 'The review could not be saved.');
        }

        return redirect()->to('/products/' . $product['slug'] . '#product-reviews')
            ->with('message', 'Thank you. Your review was submitted for approval.');
    }
}
