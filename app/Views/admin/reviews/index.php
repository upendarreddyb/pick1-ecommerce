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

<?php if (! $rows): ?>
  <div class="panel"><p>No reviews found.</p></div>
<?php else: ?>
  <div class="table-wrap review-table-wrap">
    <table class="review-table">
      <thead><tr><th>Customer</th><th>Product</th><th>Rating</th><th>Review</th><th>Submitted</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $review): ?>
          <?php $customerName = trim((string) ($review['customer_name'] ?? '')) ?: 'Customer'; ?>
          <tr>
            <td data-label="Customer">
              <strong class="review-customer-name"><?= esc($customerName) ?></strong>
              <small><?= esc($review['customer_email']) ?></small>
              <?php if (! empty($review['customer_phone'])): ?><small><?= esc($review['customer_phone']) ?></small><?php endif ?>
            </td>
            <td data-label="Product"><a href="<?= base_url('products/' . $review['product_slug']) ?>" target="_blank" rel="noopener"><?= esc($review['product_name']) ?></a><small>✓ Verified purchase</small></td>
            <td data-label="Rating"><div class="admin-review-stars" aria-label="<?= (int) $review['rating'] ?> out of 5"><?= str_repeat('★', (int) $review['rating']) ?><i><?= str_repeat('★', 5 - (int) $review['rating']) ?></i><small><?= (int) $review['rating'] ?>/5</small></div></td>
            <td data-label="Review"><p class="review-copy"><?= nl2br(esc($review['review'])) ?></p></td>
            <td data-label="Submitted"><time datetime="<?= esc($review['created_at']) ?>"><?= date('d M Y', strtotime($review['created_at'])) ?><small><?= date('h:i A', strtotime($review['created_at'])) ?></small></time></td>
            <td data-label="Status"><span class="status review-status-<?= esc($review['status']) ?>"><?= esc($review['status']) ?></span></td>
            <td data-label="Actions">
              <div class="review-actions">
                <?php if ($review['status'] !== 'approved'): ?><form method="post" action="<?= base_url('admin/reviews/' . $review['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="approved"><button class="btn review-action-approve">Approve</button></form><?php endif ?>
                <?php if ($review['status'] !== 'rejected'): ?><form method="post" action="<?= base_url('admin/reviews/' . $review['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="rejected"><button class="btn review-reject">Reject</button></form><?php endif ?>
                <form method="post" action="<?= base_url('admin/reviews/' . $review['id'] . '/delete') ?>" onsubmit="return confirm('Delete this review?')"><?= csrf_field() ?><button class="review-delete">Delete</button></form>
              </div>
            </td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
  </div>
<?php endif ?>
<?= $this->endSection() ?>
