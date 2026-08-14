<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="toolbar review-toolbar">
  <span><?= count($rows) ?> reviews</span>
  <form>
    <select name="status" onchange="this.form.submit()">
      <option value="">All statuses</option>
      <?php foreach (['pending', 'approved', 'rejected'] as $option): ?>
        <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
      <?php endforeach ?>
    </select>
  </form>
</div>

<div class="review-admin-list">
  <?php if (! $rows): ?><div class="panel"><p>No reviews found.</p></div><?php endif ?>
  <?php foreach ($rows as $review): ?>
    <article class="panel admin-review">
      <header>
        <div><h2><?= esc($review['product_name']) ?></h2><p><?= esc($review['customer_email']) ?> · <?= date('d M Y', strtotime($review['created_at'])) ?></p></div>
        <span class="status review-status-<?= esc($review['status']) ?>"><?= esc($review['status']) ?></span>
      </header>
      <div class="admin-review-stars" aria-label="<?= (int) $review['rating'] ?> out of 5"><?= str_repeat('★', (int) $review['rating']) ?><i><?= str_repeat('★', 5 - (int) $review['rating']) ?></i></div>
      <p><?= nl2br(esc($review['review'])) ?></p>
      <small>✓ Verified purchase</small>
      <footer>
        <form method="post" action="<?= base_url('admin/reviews/' . $review['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="approved"><button class="btn">Approve</button></form>
        <form method="post" action="<?= base_url('admin/reviews/' . $review['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="rejected"><button class="btn review-reject">Reject</button></form>
        <form method="post" action="<?= base_url('admin/reviews/' . $review['id'] . '/delete') ?>" onsubmit="return confirm('Delete this review?')"><?= csrf_field() ?><button class="review-delete">Delete</button></form>
      </footer>
    </article>
  <?php endforeach ?>
</div>
<?= $this->endSection() ?>
