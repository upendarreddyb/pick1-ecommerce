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
      <article class="cart-row">
        <a class="cart-thumb" href="<?= base_url('products/' . $item['slug']) ?>" aria-label="View <?= esc($item['name']) ?>">
          <?php if (! empty($item['image'])): ?>
            <img src="<?= base_url('uploads/products/' . rawurlencode(basename($item['image']))) ?>" alt="<?= esc($item['name']) ?>">
          <?php else: ?>
            <span class="placeholder"><i></i><b>Pick1</b></span>
          <?php endif ?>
        </a>

        <div>
          <h3><a href="<?= base_url('products/' . $item['slug']) ?>"><?= esc($item['name']) ?></a></h3>
          <p>₹<?= number_format($item['sale_price'] ?: $item['price']) ?></p>
        </div>

        <form method="post" action="<?= base_url('cart/update') ?>">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
          <input name="quantity" type="number" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>">
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
      <strong>₹<?= number_format($total, 2) ?></strong>
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
