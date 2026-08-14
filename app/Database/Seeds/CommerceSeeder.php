<?php
namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;
class CommerceSeeder extends Seeder { public function run(){
 $this->db->table('admins')->insert(['name'=>'Store Admin','email'=>'admin@gmail.com','password'=>password_hash('ChangeMe123!',PASSWORD_DEFAULT),'created_at'=>date('Y-m-d H:i:s')]);
 foreach([['Wellness','wellness'],['Essentials','essentials'],['Gift Sets','gift-sets']] as $c) $this->db->table('categories')->insert(['name'=>$c[0],'slug'=>$c[1],'created_at'=>date('Y-m-d H:i:s')]);
 $products=[['Daily Ritual Blend','daily-ritual-blend',1,1299,999],['Calm Evening Drops','calm-evening-drops',1,899,null],['Everyday Essentials Kit','everyday-essentials-kit',2,1999,1599],['The Wellness Gift Box','wellness-gift-box',3,2499,2199]];
 foreach($products as $p) $this->db->table('products')->insert(['name'=>$p[0],'slug'=>$p[1],'category_id'=>$p[2],'description'=>'Thoughtfully made for simple, everyday wellbeing. Clean ingredients, considered design, and no unnecessary extras.','price'=>$p[3],'sale_price'=>$p[4],'stock'=>25,'status'=>'active','created_at'=>date('Y-m-d H:i:s')]);
 }}
