<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<section class="bulk-orders-coming-soon">
  <div class="bulk-orders-mark" aria-hidden="true"><span>Pick</span><b>1</b></div>
  <p class="eyebrow">Bulk Orders</p>
  <h1>We’re working on it.</h1>
  <p>Our dedicated bulk-order experience is coming soon. For current wholesale, gifting, or large-quantity requirements, contact our team and we’ll be happy to help.</p>
  <div class="bulk-orders-actions">
    <a class="button dark" href="<?= base_url('contact') ?>">Contact us</a>
    <a class="button" href="<?= base_url('products') ?>">Continue shopping</a>
  </div>
</section>
<?= $this->endSection() ?>
