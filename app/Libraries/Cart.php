<?php
namespace App\Libraries;
use App\Models\CartItemModel;
class Cart {
 public const GST_RATE = 4.4;
 public const FREE_SHIPPING_MINIMUM = 350.0;
 public const SHIPPING_CHARGE = 49.0;
 private CartItemModel $items; public function __construct(){ $this->items=new CartItemModel(); }
 private function owner(): array { return session('customer_id') ? ['user_id'=>session('customer_id')] : ['session_id'=>session_id()]; }
 public function rows(): array { return $this->items->select('cart_items.*,products.name,products.slug,products.image,products.price,products.sale_price,products.stock')->join('products','products.id=cart_items.product_id')->where($this->owner())->findAll(); }
 public function add(int $productId,int $qty=1): void { $where=$this->owner()+['product_id'=>$productId]; $row=$this->items->where($where)->first(); $row ? $this->items->update($row['id'],['quantity'=>$row['quantity']+$qty]) : $this->items->insert($where+['quantity'=>$qty]); }
 public function change(int $productId,int $delta,int $stock): int { $where=$this->owner()+['product_id'=>$productId]; $row=$this->items->where($where)->first(); $current=(int)($row['quantity']??0); $next=max(0,min($stock,$current+$delta)); if($next===0){ if($row)$this->items->delete($row['id']); } elseif($row){ $this->items->update($row['id'],['quantity'=>$next]); } else { $this->items->insert($where+['quantity'=>$next]); } return $next; }
 public function quantities(): array { $result=[]; foreach($this->rows() as $row)$result[(int)$row['product_id']]=(int)$row['quantity']; return $result; }
 public function update(int $id,int $qty): void { $row=$this->items->where($this->owner())->find($id); if(!$row)return; $qty<1 ? $this->items->delete($id) : $this->items->update($id,['quantity'=>$qty]); }
 public function remove(int $id): void { if($this->items->where($this->owner())->find($id)) $this->items->delete($id); }
 public function count(): int { return array_sum(array_column($this->rows(),'quantity')); }
 public function total(?array $rows=null): float { return array_reduce($rows ?? $this->rows(),fn($t,$r)=>$t+((float)($r['sale_price']?:$r['price'])*$r['quantity']),0); }
 public function shipping(?float $subtotal=null): float { $subtotal ??= $this->total(); return $subtotal > 0 && $subtotal < self::FREE_SHIPPING_MINIMUM ? self::SHIPPING_CHARGE : 0.0; }
 public function payable(?float $subtotal=null): float { $subtotal ??= $this->total(); return $subtotal + $this->shipping($subtotal); }
 public function merge(int $userId): void { $guest=$this->items->where('session_id',session_id())->findAll(); foreach($guest as $g){ $existing=$this->items->where(['user_id'=>$userId,'product_id'=>$g['product_id']])->first(); if($existing)$this->items->update($existing['id'],['quantity'=>$existing['quantity']+$g['quantity']]); else $this->items->update($g['id'],['user_id'=>$userId,'session_id'=>null]); } }
 public function clear(): void { $this->items->where($this->owner())->delete(); }
}
