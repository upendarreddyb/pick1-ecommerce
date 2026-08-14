<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;use App\Models\PaymentModel;use App\Models\OrderModel;
class Payments extends BaseController {public function index(){return view('admin/payments/index',['title'=>'Payments','rows'=>(new PaymentModel())->orderBy('id','DESC')->findAll()]);}public function show($id){return view('admin/payments/show',['title'=>'Payment #'.$id,'row'=>(new PaymentModel())->find($id)]);}public function refund($id){$m=new PaymentModel();$p=$m->find($id);$m->update($id,['status'=>'refunded','notes'=>$this->request->getPost('notes')]);(new OrderModel())->update($p['order_id'],['payment_status'=>'refunded']);return redirect()->back()->with('message','Payment marked refunded.');}}
