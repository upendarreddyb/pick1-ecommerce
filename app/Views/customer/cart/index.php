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
          <small class="price-tax-note">Includes all charges</small>
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
      <span data-cart-shipping-label>Shipping<?= $shipping > 0 ? ' (free on ₹350+)' : '' ?></span>
      <strong data-cart-shipping class="<?= $shipping > 0 ? '' : 'free' ?>"><?= $shipping > 0 ? '₹' . number_format($shipping, 2) : 'Free' ?></strong>
      <span>Product prices</span>
      <strong>Include all charges</strong>
      <span class="cart-grand-label">Total</span>
      <strong class="cart-grand-value" data-cart-total>₹<?= number_format($total, 2) ?></strong>
      <a class="button dark" href="<?= base_url('checkout') ?>">Continue to checkout</a>
    </div>
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
  const rows = [...document.querySelectorAll('[data-cart-price]')];
  const subtotalOutput = document.querySelector('[data-cart-subtotal]');
  const shippingLabel = document.querySelector('[data-cart-shipping-label]');
  const shippingOutput = document.querySelector('[data-cart-shipping]');
  const totalOutput = document.querySelector('[data-cart-total]');
  if (!rows.length || !subtotalOutput || !shippingOutput || !totalOutput) return;

  const money = value => '₹' + new Intl.NumberFormat('en-IN', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value);

  const refreshTotals = () => {
    const subtotal = rows.reduce((sum, row) => {
      const input = row.querySelector('.cart-quantity');
      const quantity = Math.max(1, Number(input?.value) || 1);
      return sum + (Number(row.dataset.cartPrice) || 0) * quantity;
    }, 0);
    const shipping = subtotal > 0 && subtotal < 350 ? 49 : 0;
    subtotalOutput.textContent = money(subtotal);
    shippingLabel.textContent = shipping ? 'Shipping (free on ₹350+)' : 'Shipping';
    shippingOutput.textContent = shipping ? money(shipping) : 'Free';
    shippingOutput.classList.toggle('free', shipping === 0);
    totalOutput.textContent = money(subtotal + shipping);
  };

  document.querySelectorAll('.cart-quantity').forEach(input => {
    input.addEventListener('input', refreshTotals);
    input.addEventListener('change', () => {
      refreshTotals();
      if (input.checkValidity()) input.form.requestSubmit();
    });
  });
})();
</script>
<?= $this->endSection() ?>
