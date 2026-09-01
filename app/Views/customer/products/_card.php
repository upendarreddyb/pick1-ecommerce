<?php
$cardImages = [];
$cardImageLoading = 'eager';
if (! empty($p['image'])) {
    $cardImages[] = base_url('uploads/products/' . rawurlencode(basename($p['image'])));
}
foreach ($p['gallery'] ?? [] as $galleryImage) {
    $cardImages[] = base_url('uploads/products/' . rawurlencode(basename($galleryImage['image'])));
}
?>
<article class="product-card pick-product" data-product-card="<?= $p['id'] ?>">
  <a class="product-media" href="<?= base_url('products/' . $p['slug']) ?>">
    <?php if ($p['sale_price']): ?><span class="sale">Sale</span><?php endif ?>
    <?php if ($cardImages): ?>
      <img class="card-main-image" src="<?= esc($cardImages[0]) ?>" alt="<?= esc($p['name']) ?>" width="1080" height="1080" loading="<?= esc($cardImageLoading) ?>" decoding="async">
    <?php else: ?>
      <span class="placeholder pick-placeholder"><i></i><b>Pick<span>1</span></b></span>
    <?php endif ?>
  </a>

  <?php if (count($cardImages) > 1): ?>
    <div class="card-thumbnails" aria-label="<?= esc($p['name']) ?> images">
      <?php foreach ($cardImages as $index => $cardImage): ?>
        <button type="button" class="card-thumbnail <?= $index === 0 ? 'active' : '' ?>" data-card-image="<?= esc($cardImage) ?>" aria-label="Preview image <?= $index + 1 ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>">
          <img src="<?= esc($cardImage) ?>" alt="" width="1080" height="1080" loading="lazy" decoding="async">
        </button>
      <?php endforeach ?>
    </div>
  <?php endif ?>

  <div class="product-meta"><div><h3><a href="<?= base_url('products/' . $p['slug']) ?>"><?= esc($p['name']) ?></a></h3><p><?php if ($p['sale_price']): ?><s>₹<?= number_format($p['price']) ?></s> <?php endif ?><strong>₹<?= number_format($p['sale_price'] ?: $p['price']) ?></strong></p></div></div>
  <div class="quantity-stepper" data-product="<?= $p['id'] ?>" data-stock="<?= $p['stock'] ?>"><button type="button" data-delta="-1" aria-label="Remove one <?= esc($p['name']) ?>" <?= ($cartQuantity ?? 0) < 1 ? 'disabled' : '' ?>>−</button><output aria-live="polite"><?= (int) ($cartQuantity ?? 0) ?></output><button type="button" data-delta="1" aria-label="Add one <?= esc($p['name']) ?>" <?= $p['stock'] < 1 ? 'disabled' : '' ?>>+</button></div>
</article>
