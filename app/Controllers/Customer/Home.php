<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Libraries\Cart;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\SliderModel;
use App\Models\VideoTestimonialModel;

class Home extends BaseController
{
    public function index()
    {
        $homeContent = cache()->remember('storefront_home_content_v2', 120, static function (): array {
            $products = (new ProductModel())->where('status', 'active')->orderBy('id', 'DESC')->findAll(4);
            $galleryByProduct = [];
            $productIds = array_map(static fn (array $product): int => (int) $product['id'], $products);

            if ($productIds !== []) {
                $galleryRows = (new ProductImageModel())
                    ->whereIn('product_id', $productIds)
                    ->orderBy('product_id', 'ASC')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();

                foreach (ProductImageModel::onlyExisting($galleryRows) as $galleryImage) {
                    $galleryByProduct[(int) $galleryImage['product_id']][] = $galleryImage;
                }
            }

            foreach ($products as &$product) {
                $product['gallery'] = $galleryByProduct[(int) $product['id']] ?? [];
            }
            unset($product);

            return [
                'products'     => $products,
                'slides'       => (new SliderModel())->where('status', 'active')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
                'videoStories' => (new VideoTestimonialModel())->where('status', 'active')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
            ];
        });

        $firstSlideFilename = basename((string) ($homeContent['slides'][0]['image'] ?? ''));
        $heroPreload = $firstSlideFilename === '1787577453_7fb4defd5294d2919225.jpg'
            ? base_url('assets/images/pick1-naturally-fresh-hero.webp')
            : null;

        $body = view('customer/home', [
            'title'          => 'Pick1',
            'products'       => $homeContent['products'],
            'cartQuantities' => (new Cart())->quantities(),
            'slides'         => $homeContent['slides'],
            'videoStories'   => $homeContent['videoStories'],
            'heroPreload'    => $heroPreload,
        ]);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($body);
    }

    public function contact()
    {
        return view('customer/contact', ['title' => 'Contact us']);
    }

    public function bulkOrders()
    {
        return view('customer/bulk_orders', ['title' => 'Bulk Orders']);
    }

    public function about()
    {
        return view('customer/about', ['title' => 'About Pick1']);
    }
}
