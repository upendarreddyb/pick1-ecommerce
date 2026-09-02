<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?><style><?= file_get_contents(FCPATH . 'assets/css/order-details.css') ?></style><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="orders-page">
  <header><p class="eyebrow">Account</p><h1>Your orders</h1><p>Track deliveries, review purchases, and get support.</p></header>
  <?php if ($orders): ?>
    <div class="orders-list">
      <?php foreach ($orders as $order): ?>
        <a class="orders-list-item" href="<?= base_url('orders/' . $order['id']) ?>">
          <span class="orders-list-number"><small>Order number</small><strong><?= esc(order_number($order)) ?></strong></span>
          <span><small>Placed</small><strong><?= date('d M Y', strtotime($order['created_at'])) ?></strong></span>
          <span><small>Status</small><strong class="order-list-status is-<?= esc($order['status']) ?>"><?= esc(ucfirst($order['status'])) ?></strong></span>
          <span><small>Total</small><strong>₹<?= number_format((float) $order['total_amount'], 2) ?></strong></span>
          <b aria-hidden="true">›</b>
        </a>
      <?php endforeach ?>
    </div>
  <?php else: ?>
    <div class="orders-empty"><h2>No orders yet</h2><p>Your Pick1 purchases will appear here.</p><a href="<?= base_url('products') ?>">Shop products</a></div>
  <?php endif ?>
</section>
<?= $this->endSection() ?>
