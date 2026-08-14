<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<form class="form-card" method="post" action="<?= base_url('admin/announcement') ?>">
  <?= csrf_field() ?>
  <label class="wide">Scrolling discount message<input name="message" maxlength="220" required value="<?= esc(old('message', $row['message'] ?? 'Free shipping on selected orders · Shop Pick1 today')) ?>" placeholder="Example: Buy 4 and get 20% off"></label>
  <label>Status<select name="status"><option value="active" <?= old('status', $row['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= old('status', $row['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label>
  <label>Scroll speed (seconds)<input type="number" name="speed" min="8" max="60" value="<?= esc(old('speed', $row['speed'] ?? 22)) ?>" required><small>Lower values move faster.</small></label>
  <button class="btn wide" type="submit">Save header discount</button>
</form>
<?= $this->endSection() ?>
