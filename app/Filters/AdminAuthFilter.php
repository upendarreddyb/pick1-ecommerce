<?php
namespace App\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
class AdminAuthFilter implements FilterInterface { public function before(RequestInterface $request, $arguments=null){ return session('admin_id') ? null : redirect()->to('/admin/login'); } public function after(RequestInterface $request, ResponseInterface $response, $arguments=null){} }
