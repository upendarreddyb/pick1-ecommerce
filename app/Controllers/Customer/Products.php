<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Libraries\Cart;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductReviewModel;

class Products extends BaseController
{
    public function index()
    {
        $products = new ProductModel();

        if ($categoryId = $this->request->getGet('category')) {
            $products->where('category_id', (int) $categoryId);
        }

        if ($query = trim((string) $this->request->getGet('q'))) {
            $products->groupStart()
                ->like('name', $query)
                ->orLike('description', $query)
                ->groupEnd();
        }

        $rows = $products->where('status', 'active')->orderBy('id', 'DESC')->paginate(12);
        $galleryModel = new ProductImageModel();
        foreach ($rows as &$row) {
            $row['gallery'] = ProductImageModel::onlyExisting($galleryModel
                ->where('product_id', (int) $row['id'])
                ->orderBy('sort_order', 'ASC')
                ->findAll());
        }
        unset($row);

        $body = view('customer/products/index', [
            'title'          => 'Products',
            'products'       => $rows,
            'pager'          => $products->pager,
            'categories'     => (new CategoryModel())->findAll(),
            'cartQuantities' => (new Cart())->quantities(),
        ]);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($body);
    }

    public function show(string $slug)
    {
        $product = (new ProductModel())->where(['slug' => $slug, 'status' => 'active'])->first();

        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $cartQuantity = (new Cart())->quantities()[(int) $product['id']] ?? 0;
        $cartQuantities = (new Cart())->quantities();
        $relatedProducts = (new ProductModel())
            ->where('status', 'active')
            ->where('id !=', (int) $product['id'])
            ->orderBy('id', 'DESC')
            ->findAll(4);
        $relatedGalleryModel = new ProductImageModel();
        foreach ($relatedProducts as &$relatedProduct) {
            $relatedProduct['gallery'] = ProductImageModel::onlyExisting($relatedGalleryModel
                ->where('product_id', (int) $relatedProduct['id'])
                ->orderBy('sort_order', 'ASC')
                ->findAll());
        }
        unset($relatedProduct);
        $reviewsModel = new ProductReviewModel();
        $ratingStats = $reviewsModel
            ->select('COUNT(*) AS review_count, AVG(rating) AS rating_average')
            ->where(['product_id' => (int) $product['id'], 'status' => 'approved'])
            ->first();
        $approvedReviews = (new ProductReviewModel())
            ->select('product_reviews.*, users.name AS customer_name, users.email AS customer_email')
            ->join('users', 'users.id = product_reviews.user_id')
            ->where(['product_reviews.product_id' => (int) $product['id'], 'product_reviews.status' => 'approved'])
            ->orderBy('product_reviews.id', 'DESC')
            ->findAll();

        $userId = (int) (session('customer_id') ?? 0);
        $canReview = false;
        $currentReview = null;
        if ($userId) {
            $canReview = (bool) db_connect()->table('order_items')
                ->select('order_items.id')
                ->join('orders', 'orders.id = order_items.order_id')
                ->where('orders.user_id', $userId)
                ->where('order_items.product_id', (int) $product['id'])
                ->where('orders.payment_status', 'paid')
                ->where('orders.status', 'delivered')
                ->limit(1)
                ->get()
                ->getRowArray();
            $currentReview = (new ProductReviewModel())
                ->where(['product_id' => (int) $product['id'], 'user_id' => $userId])
                ->first();
        }

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setBody(view('customer/products/show', [
                'title'   => $product['name'],
                'product' => $product,
                'cartQuantity' => $cartQuantity,
                'ratingAverage' => (float) ($ratingStats['rating_average'] ?? 0),
                'ratingCount' => (int) ($ratingStats['review_count'] ?? 0),
                'reviews' => $approvedReviews,
                'canReview' => $canReview,
                'currentReview' => $currentReview,
                'relatedProducts' => $relatedProducts,
                'cartQuantities' => $cartQuantities,
                'gallery' => ProductImageModel::onlyExisting((new ProductImageModel())
                    ->where('product_id', (int) $product['id'])
                    ->orderBy('sort_order', 'ASC')
                    ->findAll()),
            ]));
    }
}
