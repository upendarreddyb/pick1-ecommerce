<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/product-recommendations.css?v=1') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$productImages = [];
if (! empty($product['image']) && is_file(FCPATH . 'uploads/products/' . basename($product['image']))) {
    $productImages[] = [
        'url' => base_url('uploads/products/' . rawurlencode(basename($product['image']))),
        'alt' => $product['name'] . ' main image',
    ];
}
foreach ($gallery ?? [] as $index => $galleryImage) {
    $productImages[] = [
        'url' => base_url('uploads/products/' . rawurlencode(basename($galleryImage['image']))),
        'alt' => $product['name'] . ' image ' . ($index + 2),
    ];
}
$showReviewSection = ! empty($reviews) || $canReview;
?>
<section class="product-detail">
  <div class="product-gallery">
    <div class="detail-media">
      <?php if ($productImages): ?>
        <img id="product-main-image" src="<?= esc($productImages[0]['url']) ?>" alt="<?= esc($productImages[0]['alt']) ?>" width="1080" height="1080" loading="eager" fetchpriority="high" decoding="async">
      <?php else: ?>
        <span class="placeholder large"><i></i><b>Pick1</b></span>
      <?php endif ?>
    </div>

    <?php if (count($productImages) > 1): ?>
      <div class="product-thumbnails" aria-label="Product images">
        <?php foreach ($productImages as $index => $productImage): ?>
          <button type="button" class="product-thumbnail <?= $index === 0 ? 'active' : '' ?>" data-image="<?= esc($productImage['url']) ?>" data-alt="<?= esc($productImage['alt']) ?>" aria-label="Show image <?= $index + 1 ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>">
            <img src="<?= esc($productImage['url']) ?>" alt="">
          </button>
        <?php endforeach ?>
      </div>
    <?php endif ?>
  </div>

  <div class="detail-copy">
    <h1><?= esc($product['name']) ?></h1>
    <div class="product-description" id="product-description">
      <p><?= nl2br(esc($product['description'])) ?></p>
    </div>
    <?php if (mb_strlen((string) $product['description']) > 110): ?>
      <button class="description-toggle" type="button" aria-expanded="false" aria-controls="product-description">Show more</button>
    <?php endif ?>
    <div class="product-rating" aria-label="<?= $ratingCount ? 'Rated ' . number_format($ratingAverage, 1) . ' out of 5 from ' . $ratingCount . ' reviews' : 'No reviews yet' ?>">
      <span aria-hidden="true">★</span><strong><?= $ratingCount ? number_format($ratingAverage, 1) : '0.0' ?></strong><i></i>
      <?php if ($showReviewSection): ?>
        <a href="#product-reviews"><?= $ratingCount ?> <?= $ratingCount === 1 ? 'Review' : 'Reviews' ?></a>
      <?php else: ?>
        <span class="reviews-count">No reviews yet</span>
      <?php endif ?>
    </div>
    <p class="detail-price">
      <strong>₹<?= number_format($product['sale_price'] ?: $product['price']) ?></strong>
      <?php if ($product['sale_price']): ?><s>₹<?= number_format($product['price']) ?></s><?php endif ?>
    </p>
    <p class="detail-tax-note">Includes all charges</p>
    <div class="product-cart-controls">
      <div class="quantity-stepper product-detail-stepper" data-product="<?= $product['id'] ?>" data-stock="<?= (int) $product['stock'] ?>">
        <button type="button" data-delta="-1" aria-label="Remove one <?= esc($product['name']) ?>" <?= ($cartQuantity ?? 0) < 1 ? 'disabled' : '' ?>>−</button>
        <output aria-live="polite"><?= (int) ($cartQuantity ?? 0) ?></output>
        <button type="button" data-delta="1" aria-label="Add one <?= esc($product['name']) ?>" <?= $product['stock'] < 1 || ($cartQuantity ?? 0) >= $product['stock'] ? 'disabled' : '' ?>>+</button>
      </div>
      <a class="button dark product-go-cart" href="<?= base_url('cart') ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.4 11.5h10.8l2-8H6"></path></svg>
        Go to cart
      </a>
    </div>
  </div>
</section>

<?php if ($showReviewSection): ?>
<section class="product-reviews-section" id="product-reviews">
  <header class="reviews-heading">
    <div><p class="eyebrow">Customer feedback</p><h2>Ratings &amp; Reviews</h2></div>
    <div class="reviews-summary"><strong><?= $ratingCount ? number_format($ratingAverage, 1) : '0.0' ?></strong><span aria-hidden="true">★</span><small><?= $ratingCount ?> verified <?= $ratingCount === 1 ? 'review' : 'reviews' ?></small></div>
  </header>

  <div class="reviews-layout">
    <div class="reviews-list">
      <?php if (! $reviews): ?><div class="review-empty"><h3>No reviews yet</h3><p>Verified customers can be the first to review this product.</p></div><?php endif ?>
      <?php foreach ($reviews as $review): ?>
        <?php $reviewer = $review['customer_name'] ?: preg_replace('/(^.).*(@.*$)/', '$1***$2', $review['customer_email']); ?>
        <article class="customer-review">
          <div class="review-stars" aria-label="<?= (int) $review['rating'] ?> out of 5"><?= str_repeat('★', (int) $review['rating']) ?><i><?= str_repeat('★', 5 - (int) $review['rating']) ?></i></div>
          <p><?= nl2br(esc($review['review'])) ?></p>
          <footer><strong><?= esc($reviewer) ?></strong><span>✓ Verified purchase</span><time datetime="<?= esc($review['created_at']) ?>"><?= date('d M Y', strtotime($review['created_at'])) ?></time></footer>
        </article>
      <?php endforeach ?>
    </div>

    <aside class="review-form-card">
      <?php if (! session('customer_id')): ?>
        <h3>Write a review</h3><p>Sign in to review products from your delivered orders.</p><a class="button dark" href="<?= base_url('login') ?>">Log in</a>
      <?php elseif (! $canReview): ?>
        <h3>Verified reviews only</h3><p>You can review this product after your paid order has been delivered.</p>
      <?php else: ?>
        <h3><?= $currentReview ? 'Update your review' : 'Write a review' ?></h3>
        <?php if ($currentReview): ?><p class="review-state">Current status: <strong><?= esc($currentReview['status']) ?></strong></p><?php endif ?>
        <form method="post" action="<?= base_url('products/' . $product['id'] . '/reviews') ?>">
          <?= csrf_field() ?>
          <fieldset class="rating-input"><legend>Your rating</legend>
            <?php for ($star = 5; $star >= 1; $star--): ?>
              <input id="rating-<?= $star ?>" type="radio" name="rating" value="<?= $star ?>" <?= (int) old('rating', $currentReview['rating'] ?? 0) === $star ? 'checked' : '' ?> required>
              <label for="rating-<?= $star ?>" aria-label="<?= $star ?> stars">★</label>
            <?php endfor ?>
          </fieldset>
          <label>Your review<textarea name="review" rows="5" maxlength="1000" required><?= esc(old('review', $currentReview['review'] ?? '')) ?></textarea></label>
          <button class="button dark">Submit for approval</button>
        </form>
      <?php endif ?>
    </aside>
  </div>
</section>
<?php endif ?>

<?php if (! empty($relatedProducts)): ?>
<section class="product-recommendations" aria-labelledby="product-recommendations-title">
  <header>
    <p class="eyebrow">Discover more flavors</p>
    <h2 id="product-recommendations-title">You may also like</h2>
  </header>
  <div class="product-grid">
    <?php foreach ($relatedProducts as $relatedProduct): ?>
      <?= view('customer/products/_card', ['p' => $relatedProduct, 'cartQuantity' => $cartQuantities[(int) $relatedProduct['id']] ?? 0]) ?>
    <?php endforeach ?>
  </div>
</section>
<?php endif ?>

<?php if (count($productImages) > 1): ?>
<script>
(() => {
  const mainImage = document.getElementById('product-main-image');
  document.querySelectorAll('.product-thumbnail').forEach(button => {
    button.addEventListener('click', () => {
      mainImage.src = button.dataset.image;
      mainImage.alt = button.dataset.alt;
      document.querySelectorAll('.product-thumbnail').forEach(item => {
        const active = item === button;
        item.classList.toggle('active', active);
        item.setAttribute('aria-pressed', active ? 'true' : 'false');
      });
    });
  });
})();
</script>
<?php endif ?>
<script>
(() => {
  const toggle = document.querySelector('.description-toggle');
  const description = document.getElementById('product-description');
  if (!toggle || !description) return;
  toggle.addEventListener('click', () => {
    const expanded = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
    description.classList.toggle('expanded', !expanded);
    toggle.textContent = expanded ? 'Show more' : 'Show less';
  });
})();
</script>
<?= $this->endSection() ?>
