<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
class Dashboard extends BaseController { public function index(){ $db=db_connect();return view('admin/dashboard',['title'=>'Dashboard','stats'=>['orders'=>$db->table('orders')->countAllResults(),'revenue'=>$db->table('orders')->selectSum('total_amount')->where('payment_status','paid')->get()->getRow('total_amount')?:0,'pending'=>$db->table('orders')->where('status','pending')->countAllResults(),'products'=>$db->table('products')->countAllResults()]]);}}
