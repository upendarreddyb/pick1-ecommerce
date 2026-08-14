<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<section class="stats">
  <article class="stat"><small>Total orders</small><strong><?= number_format($stats['orders']) ?></strong><p>All customer orders</p></article>
  <article class="stat"><small>Paid revenue</small><strong>₹<?= number_format($stats['revenue']) ?></strong><p>Verified payments only</p></article>
  <article class="stat"><small>Pending orders</small><strong><?= number_format($stats['pending']) ?></strong><p>Awaiting action</p></article>
  <article class="stat"><small>Active catalog</small><strong><?= number_format($stats['products']) ?></strong><p>Products in your store</p></article>
</section>
<section class="dashboard-grid">
  <div class="panel"><div class="panel-head"><h2>Store overview</h2><a href="<?= base_url('/') ?>" target="_blank">Open storefront →</a></div><div class="store-health"><div class="health-item"><span></span><strong>Store online</strong><small>Customer pages available</small></div><div class="health-item"><span></span><strong>Catalog connected</strong><small>Admin changes are live</small></div><div class="health-item"><span></span><strong>Secure checkout</strong><small>Server-side verification</small></div></div></div>
  <div class="panel"><div class="panel-head"><h2>Quick actions</h2></div><div class="quick-actions"><a href="<?= base_url('admin/products/new') ?>"><span>Add a product</span><b>+</b></a><a href="<?= base_url('admin/orders') ?>"><span>Review orders</span><b>→</b></a><a href="<?= base_url('admin/payments') ?>"><span>View payments</span><b>→</b></a></div></div>
</section>
<?= $this->endSection() ?>
