<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?><style><?= file_get_contents(FCPATH . 'assets/css/order-details.css') ?></style><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="orders-page">
  <header><p class="eyebrow">Account</p><h1>Your orders</h1><p>Track deliveries, review purchases, and get support.</p></header>
  <?php if ($orders): ?>
    <div class="orders-list">
      <?php foreach ($orders as $order): ?>
        <?php $orderStatus = (string) $order['status']; $orderStep = ['pending'=>0, 'processing'=>0, 'shipped'=>1, 'out_for_delivery'=>2, 'delivered'=>3][$orderStatus] ?? 0; ?>
        <a class="orders-list-item" href="<?= base_url('orders/' . $order['id']) ?>">
          <span class="orders-list-number"><small>Order number</small><strong><?= esc(order_number($order)) ?></strong></span>
          <span><small>Placed</small><strong><?= date('d M Y', strtotime($order['created_at'])) ?></strong></span>
          <span><small>Status</small><strong class="order-list-status is-<?= esc($orderStatus) ?>"><?= esc(['pending'=>'Pending','processing'=>'Ordered','shipped'=>'Shipped','out_for_delivery'=>'Out for delivery','delivered'=>'Delivered','cancelled'=>'Cancelled'][$orderStatus] ?? ucfirst($orderStatus)) ?></strong></span>
          <span><small>Total</small><strong>₹<?= number_format((float) $order['total_amount'], 2) ?></strong></span>
          <b aria-hidden="true">›</b>
          <?php if ($orderStatus !== 'cancelled'): ?><ol class="orders-list-progress" aria-label="Delivery progress"><?php foreach(['Ordered','Shipped','Out for delivery','Delivered'] as $index=>$label): ?><li class="<?= $index <= $orderStep ? 'is-complete' : '' ?> <?= $index === $orderStep ? 'is-current' : '' ?>"><i><?= $index <= $orderStep ? '✓' : '' ?></i><span><?= $label ?></span></li><?php endforeach ?></ol><?php endif ?>
        </a>
      <?php endforeach ?>
    </div>
  <?php else: ?>
    <div class="orders-empty"><h2>No orders yet</h2><p>Your Pick1 purchases will appear here.</p><a href="<?= base_url('products') ?>">Shop products</a></div>
  <?php endif ?>
</section>
<?= $this->endSection() ?>
