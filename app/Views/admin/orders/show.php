<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$status = (string) $row['status'];
$statusStep = ['pending'=>0, 'processing'=>0, 'shipped'=>1, 'out_for_delivery'=>2, 'delivered'=>3][$status] ?? 0;
$statusOptions = ['pending'=>'Pending payment', 'processing'=>'Ordered', 'shipped'=>'Shipped', 'out_for_delivery'=>'Out for delivery', 'delivered'=>'Delivered', 'cancelled'=>'Cancelled'];
$shippingAmount = (float) ($row['shipping_amount'] ?? 0);
$orderSubtotal = max(0, (float) $row['total_amount'] - $shippingAmount);
?>
<section class="admin-order-progress <?= $status === 'cancelled' ? 'is-cancelled' : '' ?>">
  <header><div><small>Delivery progress</small><h2><?= esc($statusOptions[$status] ?? ucfirst($status)) ?></h2></div><span><?= esc(order_number($row)) ?></span></header>
  <?php if ($status !== 'cancelled'): ?><ol><?php foreach(['Ordered','Shipped','Out for delivery','Delivered'] as $index=>$label): ?><li class="<?= $index <= $statusStep ? 'is-complete' : '' ?> <?= $index === $statusStep ? 'is-current' : '' ?>"><span><?= $index <= $statusStep ? '✓' : '' ?></span><strong><?= $label ?></strong></li><?php endforeach ?></ol><?php else: ?><p>This order has been cancelled.</p><?php endif ?>
</section>
<form class="form-card admin-order-status-form" method="post" action="<?= base_url('admin/orders/'.$row['id'].'/status') ?>"><?= csrf_field() ?><label>Update order status<select name="status"><?php foreach($statusOptions as $value=>$label): ?><option value="<?= $value ?>" <?= $status===$value?'selected':'' ?>><?= $label ?></option><?php endforeach ?></select></label><button class="btn">Update status</button></form>
<div class="table-wrap" style="margin-top:25px"><table><tr><th>Item</th><th>Quantity</th><th>Price</th></tr><?php foreach($items as $i): ?><tr><td><?= esc($i['product_name']) ?></td><td><?= $i['quantity'] ?></td><td>₹<?= number_format($i['price_at_purchase'],2) ?></td></tr><?php endforeach ?></table></div>
<div class="form-card admin-order-charges" style="margin-top:25px">
  <h3>Order charges</h3>
  <dl>
    <div><dt>Subtotal</dt><dd>₹<?= number_format($orderSubtotal, 2) ?></dd></div>
    <div><dt>Shipping</dt><dd><?= $shippingAmount > 0 ? '₹' . number_format($shippingAmount, 2) : 'Free' ?></dd></div>
    <div><dt>GST (4.4%)</dt><dd>Included in product prices</dd></div>
    <div><dt>Total</dt><dd><strong>₹<?= number_format((float) $row['total_amount'], 2) ?></strong></dd></div>
  </dl>
</div>
<?= $this->endSection() ?>
