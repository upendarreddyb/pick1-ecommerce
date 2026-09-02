<?= $this->extend('layouts/store') ?>

<?= $this->section('head') ?>
<style><?= file_get_contents(FCPATH . 'assets/css/order-details.css') ?></style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$status = strtolower((string) $order['status']);
$placedAt = strtotime((string) ($order['created_at'] ?? 'now')) ?: time();
$updatedAt = strtotime((string) ($order['updated_at'] ?? '')) ?: $placedAt;
$estimatedAt = strtotime('+7 days', $placedAt);
$publicOrderNumber = order_number($order);
$statusDetails = [
    'pending'    => ['Order placed', 'We have received your order and are waiting for payment confirmation.', 0],
    'processing' => ['Preparing your order', 'Your payment is confirmed and your Pick1 order is being packed.', 0],
    'shipped'    => ['Your order has shipped', 'Your Pick1 order is on its way to you.', 1],
    'delivered'  => ['Order delivered', 'Your order has been delivered. We hope you enjoy every Pick.', 3],
    'cancelled'  => ['Order cancelled', 'This order has been cancelled. Contact our support team if you need help.', 0],
];
[$statusTitle, $statusMessage, $completedStep] = $statusDetails[$status] ?? $statusDetails['pending'];
$steps = [
    ['Ordered', date('d M', $placedAt)],
    ['Shipped', in_array($status, ['shipped', 'delivered'], true) ? date('d M', $updatedAt) : 'Pending'],
    ['Out for delivery', $status === 'delivered' ? date('d M', $updatedAt) : 'Pending'],
    ['Delivered', $status === 'delivered' ? date('d M', $updatedAt) : date('d M', $estimatedAt)],
];
?>

<section class="order-detail-page">
  <header class="order-detail-header">
    <a href="<?= base_url('orders') ?>" aria-label="Back to your orders">←</a>
    <div><p>Order details</p><h1><?= esc($publicOrderNumber) ?></h1></div>
    <a class="order-help" href="<?= base_url('contact') ?>">Help</a>
  </header>

  <div class="order-detail-grid">
    <main>
      <section class="order-products" aria-label="Items in this order">
        <?php foreach ($items as $item): ?>
          <?php $productUrl = ! empty($item['product_slug']) ? base_url('products/' . $item['product_slug']) : base_url('products'); ?>
          <article class="order-product-row">
            <a class="order-product-image" href="<?= esc($productUrl) ?>">
              <?php if (! empty($item['product_image'])): ?>
                <img src="<?= base_url('uploads/products/' . rawurlencode(basename($item['product_image']))) ?>" alt="<?= esc($item['product_name']) ?>" width="180" height="180">
              <?php else: ?>
                <span>Pick1</span>
              <?php endif ?>
            </a>
            <div class="order-product-copy">
              <small>Order <?= esc($publicOrderNumber) ?></small>
              <h2><a href="<?= esc($productUrl) ?>"><?= esc($item['product_name']) ?></a></h2>
              <p>Quantity: <?= (int) $item['quantity'] ?></p>
              <strong>₹<?= number_format((float) $item['price_at_purchase'] * (int) $item['quantity'], 2) ?></strong>
            </div>
            <a class="order-product-open" href="<?= esc($productUrl) ?>" aria-label="View <?= esc($item['product_name']) ?>">›</a>
          </article>
        <?php endforeach ?>
      </section>

      <section class="order-progress-card <?= $status === 'cancelled' ? 'is-cancelled' : '' ?>">
        <div class="order-status-callout">
          <span aria-hidden="true"><?= $status === 'cancelled' ? '×' : '✓' ?></span>
          <div><h2><?= esc($statusTitle) ?></h2><p><?= esc($statusMessage) ?></p></div>
        </div>

        <?php if ($status !== 'cancelled'): ?>
          <p class="order-estimate">Estimated delivery by <strong><?= date('D, d M', $estimatedAt) ?></strong></p>
          <ol class="order-timeline" aria-label="Order delivery progress">
            <?php foreach ($steps as $index => [$label, $date]): ?>
              <li class="<?= $index <= $completedStep ? 'is-complete' : '' ?> <?= $index === $completedStep ? 'is-current' : '' ?>">
                <span aria-hidden="true"><?= $index <= $completedStep ? '✓' : '' ?></span>
                <strong><?= esc($label) ?></strong>
                <small><?= esc($date) ?></small>
              </li>
            <?php endforeach ?>
          </ol>
        <?php endif ?>
      </section>

      <?php if ($status === 'delivered'): ?>
        <section class="order-review-card">
          <div><p class="eyebrow">Your experience</p><h2>Review your order</h2><p>Share feedback about the products from this delivered order.</p></div>
          <div class="order-review-links">
            <?php foreach ($items as $item): ?>
              <?php if (! empty($item['product_slug'])): ?><a href="<?= base_url('products/' . $item['product_slug'] . '#product-reviews') ?>">Review <?= esc($item['product_name']) ?> →</a><?php endif ?>
            <?php endforeach ?>
          </div>
        </section>
      <?php endif ?>
    </main>

    <aside class="order-detail-sidebar">
      <section>
        <p class="order-side-label">Delivery address</p>
        <?php if ($address): ?>
          <h2><?= esc($address['full_name']) ?></h2>
          <address>
            <?= esc($address['line1']) ?><br>
            <?php if (! empty($address['line2'])): ?><?= esc($address['line2']) ?><br><?php endif ?>
            <?= esc($address['city']) ?>, <?= esc($address['state']) ?> <?= esc($address['pincode']) ?><br>
            <?= esc($address['phone']) ?>
          </address>
        <?php else: ?>
          <p>Address information is unavailable.</p>
        <?php endif ?>
      </section>

      <section class="order-payment-summary">
        <p class="order-side-label">Payment summary</p>
        <dl>
          <div><dt>Payment</dt><dd><?= esc(ucfirst((string) $order['payment_status'])) ?></dd></div>
          <div><dt>Method</dt><dd><?= esc(strtoupper((string) $order['payment_method'])) ?></dd></div>
          <div><dt>Shipping</dt><dd>Free</dd></div>
          <div class="order-summary-total"><dt>Total</dt><dd>₹<?= number_format((float) $order['total_amount'], 2) ?></dd></div>
        </dl>
      </section>

      <a class="order-support-card" href="<?= base_url('contact') ?>"><span>Need help with this order?</span><strong>Contact Pick1 support →</strong></a>
    </aside>
  </div>
</section>
<?= $this->endSection() ?>
