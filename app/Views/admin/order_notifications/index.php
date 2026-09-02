<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="notification-page-head">
  <div><p>Paid customer orders appear here. Opening this page clears the notification count on your dashboard.</p></div>
  <a class="btn" href="<?= base_url('admin/orders') ?>">View all orders</a>
</div>

<?php if ($rows): ?>
  <section class="notification-page-list">
    <?php foreach ($rows as $order): ?>
      <?php $isNew = (int) $order['id'] > (int) $lastSeenOrderId; ?>
      <a class="notification-page-row<?= $isNew ? ' is-new' : '' ?>" href="<?= base_url('admin/orders/' . $order['id']) ?>">
        <span class="notification-page-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"></path><path d="M9 8h6M9 12h6"></path></svg></span>
        <span class="notification-page-order"><span><strong><?= esc(order_number($order)) ?></strong><?php if ($isNew): ?><b>New</b><?php endif ?></span><small><?= esc($order['full_name'] ?: 'Customer') ?> · <?= esc($order['phone'] ?: 'No phone') ?></small><time><?= esc(date('d M Y, h:i A', strtotime((string) $order['created_at']))) ?></time></span>
        <span class="notification-page-payment"><small>Payment</small><strong><?= esc(ucfirst((string) $order['payment_method'])) ?></strong></span>
        <span class="notification-page-amount"><small>Amount</small><strong>₹<?= number_format((float) $order['total_amount'], 2) ?></strong></span>
        <span class="status"><?= esc(ucfirst((string) $order['status'])) ?></span>
        <span class="notification-page-open">View order <b>→</b></span>
      </a>
    <?php endforeach ?>
  </section>
<?php else: ?>
  <section class="panel notification-page-empty"><span>✓</span><h2>No order notifications</h2><p>New paid orders will appear here.</p></section>
<?php endif ?>
<?= $this->endSection() ?>
