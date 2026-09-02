<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<section class="bulk-orders-coming-soon">
  <a class="bulk-orders-logo" href="<?= base_url('/') ?>" aria-label="Pick1 home"><img src="<?= base_url('assets/images/pick1-logo.webp') ?>" alt="Pick1 Premium Flavoured Toothpicks" width="360" height="186"></a>
  <p class="eyebrow">Bulk Orders</p>
  <h1>We’re working on it.</h1>
  <p>Our dedicated bulk-order experience is coming soon. For current wholesale, gifting, or large-quantity requirements, contact our team and we’ll be happy to help.</p>
  <div class="bulk-orders-actions">
    <a class="button dark" href="<?= base_url('contact') ?>">Contact us</a>
    <a class="button" href="<?= base_url('products') ?>">Continue shopping</a>
  </div>
</section>
<?= $this->endSection() ?>
