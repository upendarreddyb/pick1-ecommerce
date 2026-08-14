<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Customer\\Home::index');
$routes->get('products', 'Customer\\Products::index');
$routes->get('products/(:segment)', 'Customer\\Products::show/$1');
$routes->get('contact', 'Customer\\Contact::index');
$routes->post('contact', 'Customer\\Contact::create');
$routes->get('about', 'Customer\\Home::about');
$routes->get('bulk-orders', 'Customer\\Home::bulkOrders');
$routes->get('policies', 'Customer\\Policies::index');
$routes->get('disclaimer', 'Customer\\Policies::disclaimer');
$routes->get('shipping-policy', 'Customer\\Policies::index');
$routes->get('terms-of-service', 'Customer\\Policies::index');
$routes->get('privacy-policy', 'Customer\\Policies::index');
$routes->get('return-refund-policy', 'Customer\\Policies::index');
$routes->match(['GET', 'POST'], 'login', 'Customer\\Auth::login');
$routes->match(['GET', 'POST'], 'verify-otp', 'Customer\\Auth::verify');
$routes->post('logout', 'Customer\\Auth::logout');
$routes->get('cart', 'Customer\\Cart::index');
$routes->post('cart/add', 'Customer\\Cart::add');
$routes->post('cart/change', 'Customer\\Cart::change');
$routes->post('cart/update', 'Customer\\Cart::update');
$routes->post('cart/remove', 'Customer\\Cart::remove');
$routes->group('', ['filter' => 'customerAuth'], static function ($routes) {
    $routes->match(['GET', 'POST'], 'checkout', 'Customer\\Checkout::index');
    $routes->post('checkout/payment/verify', 'Customer\\Checkout::verifyPayment');
    $routes->get('orders', 'Customer\\Orders::index');
    $routes->get('orders/(:num)', 'Customer\\Orders::show/$1');
    $routes->post('products/(:num)/reviews', 'Customer\\Reviews::create/$1');
});
$routes->group('admin', static function ($routes) {
    $routes->match(['GET', 'POST'], 'login', 'Admin\\Auth::login');
    $routes->get('logout', 'Admin\\Auth::logout');
});
$routes->group('admin', ['filter' => 'adminAuth'], static function ($routes) {
    $routes->get('/', 'Admin\\Dashboard::index');
    $routes->match(['GET', 'POST'], 'password', 'Admin\\Auth::password');
    $routes->resource('products', ['controller' => 'Admin\\Products']);
    $routes->resource('categories', ['controller' => 'Admin\\Categories']);
    $routes->resource('sliders', ['controller' => 'Admin\\Sliders']);
    $routes->resource('video-stories', ['controller' => 'Admin\\VideoStories']);
    $routes->get('orders', 'Admin\\Orders::index');
    $routes->get('orders/(:num)', 'Admin\\Orders::show/$1');
    $routes->post('orders/(:num)/status', 'Admin\\Orders::status/$1');
    $routes->get('payments', 'Admin\\Payments::index');
    $routes->get('payments/(:num)', 'Admin\\Payments::show/$1');
    $routes->post('payments/(:num)/refund', 'Admin\\Payments::refund/$1');
    $routes->get('reviews', 'Admin\\Reviews::index');
    $routes->post('reviews/(:num)/status', 'Admin\\Reviews::status/$1');
    $routes->post('reviews/(:num)/delete', 'Admin\\Reviews::delete/$1');
    $routes->get('announcement', 'Admin\\Announcement::index');
    $routes->post('announcement', 'Admin\\Announcement::save');
    $routes->get('contact-messages', 'Admin\\ContactMessages::index');
    $routes->post('contact-messages/(:num)/status', 'Admin\\ContactMessages::status/$1');
});
