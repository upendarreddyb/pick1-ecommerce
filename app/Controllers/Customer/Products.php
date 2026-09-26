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
            'metaTitle'      => 'Flavoured Toothpicks | Shop PICK1',
            'metaDescription'=> 'Shop PICK1 premium flavoured toothpicks in refreshing mint, coffee, clove, and pan masala flavours, crafted from food-grade birchwood.',
            'canonicalUrl'   => base_url('products'),
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

        $seoBySlug = [
            'coffee' => [
                'title' => 'PICK1 Coffee Flavoured Toothpicks | Rich Coffee Flavour',
                'description' => 'Enjoy the rich coffee flavour of PICK1 Coffee Flavoured Toothpicks. Premium flavoured toothpicks designed for a refreshing and satisfying experience.',
                'schemaName' => 'PICK1 Coffee Flavoured Toothpicks',
            ],
            'clove' => [
                'title' => 'PICK1 Clove Flavoured Toothpicks | Aromatic Clove',
                'description' => 'Enjoy the warm, aromatic taste of PICK1 Clove Flavoured Toothpicks. Premium flavoured toothpicks for a refreshing and satisfying experience.',
                'schemaName' => 'PICK1 Clove Flavoured Toothpicks',
            ],
            'pan-masala' => [
                'title' => 'PICK1 Pan Masala Flavoured Toothpicks | Rich Pan Flavour',
                'description' => 'Experience the rich pan-inspired flavour of PICK1 Pan Masala Flavoured Toothpicks. A convenient choice for a refreshing and satisfying flavour experience.',
                'schemaName' => 'PICK1 Pan Masala Flavoured Toothpicks',
            ],
            'mint' => [
                'title' => 'PICK1 Mint Flavoured Toothpicks | Fresh Mint Flavour',
                'description' => 'Enjoy the crisp, refreshing taste of PICK1 Mint Flavoured Toothpicks. Premium flavoured toothpicks designed for a clean and refreshing flavour experience.',
                'schemaName' => 'PICK1 Mint Flavoured Toothpicks',
            ],
        ];
        $fallbackDescription = trim(preg_replace('/\s+/', ' ', strip_tags((string) $product['description'])));
        $seo = $seoBySlug[$slug] ?? [
            'title' => 'PICK1 ' . $product['name'] . ' | Premium Flavoured Toothpicks',
            'description' => $fallbackDescription !== ''
                ? mb_substr($fallbackDescription, 0, 155)
                : 'Shop PICK1 ' . $product['name'] . ', premium flavoured toothpicks crafted from food-grade birchwood for a clean, refreshing experience.',
            'schemaName' => 'PICK1 ' . $product['name'],
        ];
        $mainImageFilename = basename((string) ($product['image'] ?? ''));
        $metaImage = $mainImageFilename !== '' && is_file(FCPATH . 'uploads/products/' . $mainImageFilename)
            ? base_url('uploads/products/' . rawurlencode($mainImageFilename))
            : base_url('assets/images/pick1-logo-2026.webp');

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
                'metaTitle' => $seo['title'],
                'metaDescription' => $seo['description'],
                'canonicalUrl' => base_url('products/' . $product['slug']),
                'metaImage' => $metaImage,
                'ogType' => 'product',
                'schemaName' => $seo['schemaName'],
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
