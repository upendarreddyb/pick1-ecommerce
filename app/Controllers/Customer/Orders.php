<?php
namespace App\Controllers\Customer;
use App\Controllers\BaseController; use App\Models\{OrderModel,OrderItemModel};
class Orders extends BaseController { public function index(){return view('customer/orders/index',['title'=>'Your orders','orders'=>(new OrderModel())->where('user_id',session('customer_id'))->orderBy('id','DESC')->findAll()]);} public function show($id){$o=(new OrderModel())->where(['id'=>$id,'user_id'=>session('customer_id')])->first();if(!$o)throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();return view('customer/orders/show',['title'=>'Order #'.$id,'order'=>$o,'items'=>(new OrderItemModel())->where('order_id',$id)->findAll()]);}}
