<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<section class="checkout-shell">
  <div class="checkout-form-panel">
    <p class="checkout-kicker">Secure checkout</p>
    <h1>Delivery details</h1>
    <form method="post" id="checkout-form">
      <?= csrf_field() ?>
      <div class="checkout-fields">
        <label>Full name<input name="full_name" value="<?= old('full_name') ?>" required autocomplete="name"></label>
        <label>Phone<input name="phone" inputmode="tel" value="<?= old('phone') ?>" required autocomplete="tel"></label>
        <label class="wide">Address<input name="line1" value="<?= old('line1') ?>" required autocomplete="address-line1"></label>
        <label class="wide">Apartment / landmark <span>(optional)</span><input name="line2" value="<?= old('line2') ?>" autocomplete="address-line2"></label>
        <label>City<input name="city" value="<?= old('city') ?>" required autocomplete="address-level2"></label>
        <label>State<input name="state" value="<?= old('state') ?>" required autocomplete="address-level1"></label>
        <label>PIN code<input name="pincode" inputmode="numeric" value="<?= old('pincode') ?>" required autocomplete="postal-code"></label>
      </div>

      <fieldset class="payment-choice">
        <legend>Choose how you want to pay</legend>
        <label class="payment-option"><input type="radio" name="payment_method" value="razorpay" checked><span class="payment-logo razorpay-mark">R</span><span><strong>Razorpay</strong><small>Cards, UPI, netbanking and wallets</small></span><i>›</i></label>
      </fieldset>
      <button class="checkout-pay-button">Continue to payment · ₹<?= number_format($total,2) ?></button>
      <p class="secure-note">🔒 Payment details are handled securely by Razorpay.</p>
    </form>
  </div>

  <aside class="checkout-summary">
    <div class="summary-title"><h2>Your order</h2><a href="<?= base_url('cart') ?>">Edit cart</a></div>
    <div class="summary-items">
      <?php foreach($items as $item): ?>
        <article class="summary-item">
          <div class="summary-thumb"><?php if($item['image']): ?><img src="<?= base_url('uploads/products/' . rawurlencode(basename($item['image']))) ?>" alt="<?= esc($item['name']) ?>"><?php else: ?><span>Pick1</span><?php endif ?><b><?= $item['quantity'] ?></b></div>
          <div><h3><?= esc($item['name']) ?></h3><p>Qty: <?= $item['quantity'] ?></p></div>
          <strong>₹<?= number_format(($item['sale_price']?:$item['price'])*$item['quantity'],2) ?></strong>
        </article>
      <?php endforeach ?>
    </div>
    <dl class="summary-totals">
      <div><dt>Subtotal</dt><dd>₹<?= number_format($subtotal, 2) ?></dd></div>
      <div><dt>Shipping</dt><dd class="<?= $shipping > 0 ? '' : 'free' ?>"><?= $shipping > 0 ? '₹' . number_format($shipping, 2) : 'Free' ?></dd></div>
      <div><dt>GST (4.4%)</dt><dd>Included</dd></div>
      <div class="grand-total"><dt>Total</dt><dd><small>INR</small> ₹<?= number_format($total, 2) ?></dd></div>
    </dl>
  </aside>
</section>
<?= $this->endSection() ?>
