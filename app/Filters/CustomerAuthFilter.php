<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
class CustomerAuthFilter implements FilterInterface { public function before(RequestInterface $request, $arguments=null){ return session('customer_id') ? null : redirect()->to('/login')->with('message','Please sign in to continue.'); } public function after(RequestInterface $request, ResponseInterface $response, $arguments=null){} }
