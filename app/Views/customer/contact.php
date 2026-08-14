<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<section class="contact" id="bulk-orders">
  <div>
    <p class="eyebrow">Contact &amp; Bulk Orders</p>
    <h1>We’d love to<br><em>hear from you.</em></h1>
    <p>Questions about an order, our products, or wholesale and bulk pricing? Send us your required quantity and delivery location.</p>
    <div class="contact-details">
      <p><strong>Phone number</strong><a href="tel:+919703255444">+91 97032 55444</a></p>
      <p><strong>Email</strong><a href="mailto:support@pick1.in">support@pick1.in</a><span> or </span><a href="mailto:info@pick1.in">info@pick1.in</a></p>
    </div>
  </div>
  <form method="post" action="<?= base_url('contact') ?>">
    <?= csrf_field() ?>
    <label>Name <span aria-hidden="true">*</span><input type="text" name="name" maxlength="100" autocomplete="name" required value="<?= esc(old('name')) ?>"></label>
    <label>Email <span aria-hidden="true">*</span><input type="email" name="email" maxlength="190" autocomplete="email" required value="<?= esc(old('email')) ?>"></label>
    <label>Message <span aria-hidden="true">*</span><textarea name="message" rows="6" maxlength="2000" required placeholder="For bulk orders, include the product, quantity and delivery location."><?= esc(old('message')) ?></textarea></label>
    <button type="submit" class="button dark">Send message</button>
  </form>
</section>
<?= $this->endSection() ?>
