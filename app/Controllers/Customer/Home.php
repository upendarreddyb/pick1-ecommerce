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
        $products = (new ProductModel())->where('status', 'active')->orderBy('id', 'DESC')->findAll(4);
        $galleryModel = new ProductImageModel();
        foreach ($products as &$product) {
            $product['gallery'] = $galleryModel
                ->where('product_id', (int) $product['id'])
                ->orderBy('sort_order', 'ASC')
                ->findAll();
        }
        unset($product);

        $body = view('customer/home', [
            'title'          => 'Pick1',
            'products'       => $products,
            'cartQuantities' => (new Cart())->quantities(),
            'slides'         => (new SliderModel())->where('status', 'active')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
            'videoStories'   => (new VideoTestimonialModel())->where('status', 'active')->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll(),
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
