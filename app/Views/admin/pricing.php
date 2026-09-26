<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<form class="form-card" method="post" action="<?= base_url('admin/pricing') ?>">
  <?= csrf_field() ?>
  <label>Shipping charge (₹)
    <input type="number" name="shipping_charge" min="0" max="100000" step="0.01" value="<?= esc(old('shipping_charge', $settings['shipping_charge'])) ?>" required>
    <small>Charged when the product subtotal is below the free-shipping minimum.</small>
  </label>
  <label>Free shipping minimum (₹)
    <input type="number" name="free_shipping_minimum" min="0" max="1000000" step="0.01" value="<?= esc(old('free_shipping_minimum', $settings['free_shipping_minimum'])) ?>" required>
    <small>Enter 0 to make shipping free for every non-empty order.</small>
  </label>
  <label>GST rate (%)
    <input type="number" name="gst_rate" min="0" max="100" step="0.01" value="<?= esc(old('gst_rate', $settings['gst_rate'])) ?>" required>
    <small>GST remains included in product prices; this controls the included GST amount shown to customers.</small>
  </label>
  <div class="wide"><small>Changes apply immediately to new cart and checkout calculations. Existing orders keep their saved totals.</small></div>
  <button class="btn wide" type="submit">Save shipping &amp; GST</button>
</form>
<?= $this->endSection() ?>
