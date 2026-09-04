<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Libraries\Cart as CartLibrary;
use App\Models\ProductImageModel;
use App\Models\ProductModel;

class Cart extends BaseController
{
    private function cart(): CartLibrary { return new CartLibrary(); }

    public function index()
    {
        $cart = $this->cart();
        $items = $cart->rows();
        $pricing = $cart->pricing($items);
        $excluded = array_map('intval', array_column($items, 'product_id'));
        $products = new ProductModel();
        $products->where('status', 'active');
        if ($excluded) $products->whereNotIn('id', $excluded);
        $recommendations = $products->orderBy('id', 'DESC')->findAll(4);
        $gallery = new ProductImageModel();
        foreach ($recommendations as &$product) $product['gallery'] = ProductImageModel::onlyExisting($gallery->where('product_id', (int) $product['id'])->orderBy('sort_order', 'ASC')->findAll());
        unset($product);
        return view('customer/cart/index', ['title' => 'Your cart', 'items' => $items] + $pricing + ['recommendations' => $recommendations, 'cartQuantities' => $cart->quantities()]);
    }

    public function add()
    {
        $product = (new ProductModel())->where('status', 'active')->find((int) $this->request->getPost('product_id'));
        if (! $product) return $this->response->setStatusCode(404)->setJSON(['message' => 'Product unavailable', 'csrfHash' => csrf_hash()]);
        $cart = $this->cart();
        $quantity = max(1, (int) $this->request->getPost('quantity'));
        $current = $cart->quantities()[(int) $product['id']] ?? 0;
        $productQuantity = $cart->change((int) $product['id'], $quantity, (int) $product['stock']);
        return $this->pricingResponse($cart, ['productQuantity' => $productQuantity, 'message' => $productQuantity === $current ? 'Maximum stock reached' : $product['name'] . ' added to cart']);
    }

    public function change()
    {
        $productId = (int) $this->request->getPost('product_id');
        $delta = (int) $this->request->getPost('delta');
        if (! in_array($delta, [-1, 1], true)) return $this->response->setStatusCode(422)->setJSON(['message' => 'Invalid quantity change', 'csrfHash' => csrf_hash()]);
        $product = (new ProductModel())->where('status', 'active')->find($productId);
        if (! $product) return $this->response->setStatusCode(404)->setJSON(['message' => 'Product unavailable', 'csrfHash' => csrf_hash()]);
        $cart = $this->cart();
        $quantity = $cart->change($productId, $delta, (int) $product['stock']);
        return $this->pricingResponse($cart, ['productQuantity' => $quantity, 'message' => $delta > 0 ? $product['name'] . ' added to cart' : 'Cart updated']);
    }

    public function update()
    {
        $cart = $this->cart();
        $cart->update((int) $this->request->getPost('id'), (int) $this->request->getPost('quantity'));
        if ($this->request->isAJAX()) return $this->pricingResponse($cart, ['message' => 'Cart updated']);
        return redirect()->to('/cart');
    }

    public function remove()
    {
        $cart = $this->cart();
        $cart->remove((int) $this->request->getPost('id'));
        if ($this->request->isAJAX()) return $this->pricingResponse($cart, ['message' => 'Item removed']);
        return redirect()->to('/cart');
    }

    public function coupon()
    {
        $cart = $this->cart();
        $code = strtoupper(trim((string) $this->request->getPost('code')));
        if ((bool) $this->request->getPost('remove') || $code === '') {
            session()->remove('coupon_code');
            return $this->pricingResponse($cart, ['message' => 'Coupon removed']);
        }
        if (! $cart->coupon($code)) return $this->response->setStatusCode(422)->setJSON(['message' => 'Enter a valid active coupon code.', 'csrfHash' => csrf_hash()]);
        session()->set('coupon_code', $code);
        return $this->pricingResponse($cart, ['message' => 'Coupon applied — 10% discount']);
    }

    private function pricingResponse(CartLibrary $cart, array $extra = [])
    {
        return $this->response->setJSON(['ok' => true, 'count' => $cart->count(), 'csrfHash' => csrf_hash()] + $cart->pricing() + $extra);
    }
}
