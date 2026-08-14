<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;use App\Models\{OrderModel,OrderItemModel};
class Orders extends BaseController {public function index(){ $m=new OrderModel();if($s=$this->request->getGet('status'))$m->where('status',$s);return view('admin/orders/index',['title'=>'Orders','rows'=>$m->select('orders.*,users.email')->join('users','users.id=orders.user_id')->orderBy('id','DESC')->findAll()]);}public function show($id){return view('admin/orders/show',['title'=>'Order #'.$id,'row'=>(new OrderModel())->find($id),'items'=>(new OrderItemModel())->where('order_id',$id)->findAll()]);}public function status($id){(new OrderModel())->update($id,['status'=>$this->request->getPost('status')]);return redirect()->back()->with('message','Order updated.');}}
