<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/cart-recommendations.css?v=1') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="narrow">
  <p class="eyebrow">Your bag</p>
  <h1>Selected with care.</h1>

  <?php if (! $items): ?>
    <div class="empty">
      <p>Your bag is waiting for something good.</p>
      <a class="button dark" href="<?= base_url('products') ?>">Browse products</a>
    </div>
  <?php else: ?>
    <?php foreach ($items as $item): ?>
      <?php $unitPrice = (float) ($item['sale_price'] ?: $item['price']); ?>
      <article class="cart-row" data-cart-price="<?= $unitPrice ?>">
        <a class="cart-thumb" href="<?= base_url('products/' . $item['slug']) ?>" aria-label="View <?= esc($item['name']) ?>">
          <?php if (! empty($item['image'])): ?>
            <img src="<?= base_url('uploads/products/' . rawurlencode(basename($item['image']))) ?>" alt="<?= esc($item['name']) ?>">
          <?php else: ?>
            <span class="placeholder"><i></i><b>Pick1</b></span>
          <?php endif ?>
        </a>

        <div>
          <h3><a href="<?= base_url('products/' . $item['slug']) ?>"><?= esc($item['name']) ?></a></h3>
          <p>₹<?= number_format($unitPrice) ?></p>
          <strong class="price-tax-note">Include all charges</strong>
        </div>

        <form method="post" action="<?= base_url('cart/update') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
          <input class="cart-quantity" name="quantity" type="number" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>">
          <button>Update</button>
        </form>

        <form method="post" action="<?= base_url('cart/remove') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
          <button>Remove</button>
        </form>
      </article>
    <?php endforeach ?>

    <div class="cart-total">
      <span>Subtotal</span>
      <strong data-cart-subtotal>₹<?= number_format($subtotal, 2) ?></strong>
      <span data-cart-shipping-label>Shipping<?= $shipping > 0 ? ' (free on ₹349+)' : '' ?></span>
      <strong data-cart-shipping class="<?= $shipping > 0 ? '' : 'free' ?>"><?= $shipping > 0 ? '₹' . number_format($shipping, 2) : 'Free' ?></strong>
      <span data-cart-discount-label <?= $discount <= 0 ? 'hidden' : '' ?>>Coupon discount<?= $couponCode ? ' (' . esc($couponCode) . ')' : '' ?></span>
      <strong data-cart-discount <?= $discount <= 0 ? 'hidden' : '' ?>>−₹<?= number_format($discount, 2) ?></strong>
      <span>Product prices</span>
      <strong>Include all charges</strong>
      <span class="cart-grand-label">Total</span>
      <strong class="cart-grand-value" data-cart-total>₹<?= number_format($total, 2) ?></strong>
      <a class="button dark" href="<?= base_url('checkout') ?>">Continue to checkout</a>
    </div>
    <form class="cart-coupon" method="post" action="<?= base_url('cart/coupon') ?>">
      <?= csrf_field() ?>
      <label for="coupon-code">Coupon code</label>
      <div><input id="coupon-code" name="code" maxlength="50" value="<?= esc($couponCode ?? '') ?>" placeholder="Enter coupon code"><button type="submit" data-apply-coupon><?= $couponCode ? 'Update' : 'Apply' ?></button><button type="button" data-remove-coupon <?= $couponCode ? '' : 'hidden' ?>>Remove</button></div>
      <small data-coupon-message><?= $couponCode ? '10% discount applied.' : 'Enter an active coupon for 10% off.' ?></small>
    </form>
  <?php endif ?>
</section>
<?php if (! empty($recommendations)): ?>
<section class="cart-recommendations" aria-labelledby="cart-recommendations-title">
  <header><p class="eyebrow">You may also like</p><h2 id="cart-recommendations-title">Add more freshness.</h2></header>
  <div class="product-grid"><?php foreach ($recommendations as $p): ?><?= view('customer/products/_card', ['p' => $p, 'cartQuantity' => $cartQuantities[(int) $p['id']] ?? 0]) ?><?php endforeach ?></div>
</section>
<?php endif ?>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(() => {
  const subtotalOutput = document.querySelector('[data-cart-subtotal]');
  const shippingLabel = document.querySelector('[data-cart-shipping-label]');
  const shippingOutput = document.querySelector('[data-cart-shipping]');
  const discountLabel = document.querySelector('[data-cart-discount-label]');
  const discountOutput = document.querySelector('[data-cart-discount]');
  const totalOutput = document.querySelector('[data-cart-total]');
  const couponForm = document.querySelector('.cart-coupon');
  const couponMessage = document.querySelector('[data-coupon-message]');
  const couponRemove = document.querySelector('[data-remove-coupon]');
  const couponApply = document.querySelector('[data-apply-coupon]');
  if (!subtotalOutput || !shippingOutput || !totalOutput) return;

  const money = value => '₹' + new Intl.NumberFormat('en-IN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);

  const updateTotals = payload => {
    subtotalOutput.textContent = money(payload.subtotal);
    shippingLabel.textContent = payload.shipping ? 'Shipping (free on ₹349+)' : 'Shipping';
    shippingOutput.textContent = payload.shipping ? money(payload.shipping) : 'Free';
    shippingOutput.classList.toggle('free', payload.shipping === 0);
    const hasDiscount = Number(payload.discount) > 0;
    discountLabel.hidden = !hasDiscount;
    discountOutput.hidden = !hasDiscount;
    discountLabel.textContent = 'Coupon discount' + (payload.couponCode ? ` (${payload.couponCode})` : '');
    discountOutput.textContent = '−' + money(payload.discount || 0);
    totalOutput.textContent = money(payload.total);
    couponRemove.hidden = !payload.couponCode;
    couponApply.textContent = payload.couponCode ? 'Update' : 'Apply';
  };

  const submitAjax = async (form, extra = {}) => {
    const data = new FormData(form);
    Object.entries(extra).forEach(([key, value]) => data.set(key, value));
    data.set(CSRF.name, CSRF.hash);
    const response = await fetch(form.action, {method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'}, body: data});
    const payload = await response.json();
    if (payload.csrfHash) CSRF.hash = payload.csrfHash;
    if (!response.ok) throw new Error(payload.message || 'Unable to update cart.');
    document.dispatchEvent(new CustomEvent('cart:updated', {detail: payload}));
    return payload;
  };

  document.addEventListener('cart:updated', event => updateTotals(event.detail));
  document.querySelectorAll('.cart-row form[action$="cart/update"]').forEach(form => {
    form.addEventListener('submit', async event => {
      event.preventDefault();
      const button = form.querySelector('button');
      button.disabled = true;
      try { await submitAjax(form); couponMessage.textContent = 'Cart updated.'; }
      catch (error) { couponMessage.textContent = error.message; }
      finally { button.disabled = false; }
    });
    form.querySelector('.cart-quantity')?.addEventListener('change', event => {
      if (event.currentTarget.checkValidity()) form.requestSubmit();
    });
  });

  couponForm?.addEventListener('submit', async event => {
    event.preventDefault();
    try { const payload = await submitAjax(couponForm); couponMessage.textContent = payload.message; }
    catch (error) { couponMessage.textContent = error.message; }
  });
  couponForm?.querySelector('[data-remove-coupon]')?.addEventListener('click', async () => {
    try { const payload = await submitAjax(couponForm, {remove: '1', code: ''}); couponForm.querySelector('input').value = ''; couponMessage.textContent = payload.message; }
    catch (error) { couponMessage.textContent = error.message; }
  });
})();
</script>
<?= $this->endSection() ?>
