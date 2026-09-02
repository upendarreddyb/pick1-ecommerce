<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="notification-page-head">
  <div><p>Paid customer orders appear here. Opening this page clears the notification count on your dashboard.</p></div>
  <a class="btn" href="<?= base_url('admin/orders') ?>">View all orders</a>
</div>

<?php if ($rows): ?>
  <div class="table-wrap notification-table-wrap">
    <table class="notification-table">
      <thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th>Payment</th><th>Amount</th><th>Status</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($rows as $order): ?>
          <?php $isNew = (int) $order['id'] > (int) $lastSeenOrderId; ?>
          <tr class="<?= $isNew ? 'is-new' : '' ?>">
            <td><a class="notification-order-number" href="<?= base_url('admin/orders/' . $order['id']) ?>"><?= esc(order_number($order)) ?></a><?php if ($isNew): ?><span class="notification-new-badge">New</span><?php endif ?></td>
            <td><?= esc($order['full_name'] ?: 'Customer') ?></td>
            <td><?= esc($order['phone'] ?: '—') ?></td>
            <td><?= esc(ucfirst((string) $order['payment_method'])) ?></td>
            <td><strong>₹<?= number_format((float) $order['total_amount'], 2) ?></strong></td>
            <td><span class="status"><?= esc(ucfirst((string) $order['status'])) ?></span></td>
            <td><?= esc(date('d M Y, h:i A', strtotime((string) $order['created_at']))) ?></td>
            <td><a class="notification-view-link" href="<?= base_url('admin/orders/' . $order['id']) ?>">View →</a></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <section class="panel notification-page-empty"><span>✓</span><h2>No order notifications</h2><p>New paid orders will appear here.</p></section>
<?php endif ?>
<?= $this->endSection() ?>
