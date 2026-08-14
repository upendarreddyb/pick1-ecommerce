<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="page-actions">
  <p>Upload customer testimonial videos shown below “Why Pick1?” on the homepage.</p>
  <a class="btn" href="<?= base_url('admin/video-stories/new') ?>">Add video story</a>
</div>

<div class="table-card">
  <table>
    <thead><tr><th>Preview</th><th>Customer</th><th>Rating</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php if (empty($rows)): ?>
      <tr><td colspan="6" class="empty-state">No video stories yet. Add the first customer video.</td></tr>
    <?php endif ?>
    <?php foreach ($rows as $row): ?>
      <tr>
        <td>
          <?php if (! empty($row['video'])): ?><video class="video-story-admin-thumb" muted preload="metadata" <?= ! empty($row['poster']) ? 'poster="' . esc(base_url('uploads/video-stories/' . rawurlencode(basename($row['poster'])))) . '"' : '' ?>>
            <source src="<?= esc(base_url('uploads/video-stories/' . rawurlencode(basename($row['video'])))) ?>">
          </video><?php else: ?><span class="external-video-badge"><?= esc(ucfirst($row['provider'] ?? 'Link')) ?></span><?php endif ?>
        </td>
        <td><strong><?= esc($row['customer_name']) ?></strong><small class="table-subtitle"><?= esc($row['title'] ?? '') ?></small></td>
        <td><span class="admin-stars"><?= str_repeat('★', (int) $row['rating']) ?></span></td>
        <td><?= (int) $row['sort_order'] ?></td>
        <td><span class="status status-<?= esc($row['status']) ?>"><?= esc(ucfirst($row['status'])) ?></span></td>
        <td><div class="slider-actions"><a href="<?= base_url('admin/video-stories/' . $row['id'] . '/edit') ?>">Edit</a><form method="post" action="<?= base_url('admin/video-stories/' . $row['id']) ?>" onsubmit="return confirm('Delete this video story?')"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button type="submit">Delete</button></form></div></td>
      </tr>
    <?php endforeach ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
