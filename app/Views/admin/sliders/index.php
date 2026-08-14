<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="toolbar">
  <span><?= count($rows) ?> slides</span>
  <a class="btn" href="<?= base_url('admin/sliders/new') ?>">Add slide</a>
</div>

<?php if (! $rows): ?>
  <div class="panel slider-empty"><h2>No uploaded slides yet</h2><p>Add the first slide to replace the default homepage banners.</p></div>
<?php else: ?>
  <div class="table-wrap"><table class="slider-table">
    <thead><tr><th>Preview</th><th>Content</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody><?php foreach ($rows as $row): ?><tr>
      <td><img class="slider-thumb" src="<?= base_url('uploads/sliders/' . rawurlencode(basename($row['image']))) ?>" alt=""></td>
      <td><strong><?= esc($row['title']) ?></strong><?php if ($row['eyebrow']): ?><small><?= esc($row['eyebrow']) ?></small><?php endif ?></td>
      <td><?= (int) $row['sort_order'] ?></td>
      <td><span class="status <?= $row['status'] === 'inactive' ? 'status-inactive' : '' ?>"><?= esc($row['status']) ?></span></td>
      <td><div class="slider-actions"><a href="<?= base_url('admin/sliders/' . $row['id'] . '/edit') ?>">Edit</a><form method="post" action="<?= base_url('admin/sliders/' . $row['id']) ?>" onsubmit="return confirm('Delete this homepage slide?')"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button type="submit">Delete</button></form></div></td>
    </tr><?php endforeach ?></tbody>
  </table></div>
<?php endif ?>

<?= $this->endSection() ?>
