<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<form class="form-card slider-form" method="post" enctype="multipart/form-data" action="<?= isset($row) ? base_url('admin/sliders/' . $row['id']) : base_url('admin/sliders') ?>">
  <?= csrf_field() ?>
  <?php if (isset($row)): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>

  <label>Small heading<input name="eyebrow" maxlength="120" value="<?= esc(old('eyebrow', $row['eyebrow'] ?? '')) ?>" placeholder="Natural everyday ritual"></label>
  <label>Display order<input type="number" name="sort_order" value="<?= esc(old('sort_order', $row['sort_order'] ?? 0)) ?>" required></label>
  <label class="wide">Main heading<input name="title" maxlength="180" value="<?= esc(old('title', $row['title'] ?? '')) ?>" required placeholder="Naturally fresh"></label>
  <label class="wide">Description<textarea name="description" maxlength="300" rows="3" placeholder="Premium flavored toothpicks, thoughtfully made."><?= esc(old('description', $row['description'] ?? '')) ?></textarea></label>
  <label>Button text<input name="button_text" maxlength="80" value="<?= esc(old('button_text', $row['button_text'] ?? 'Shop Now')) ?>"></label>
  <label>Button link<input name="button_url" maxlength="255" value="<?= esc(old('button_url', $row['button_url'] ?? 'products')) ?>" placeholder="products"></label>
  <label>Status<select name="status"><option value="active" <?= old('status', $row['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= old('status', $row['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label>

  <div class="wide slider-image-editor">
    <div class="slider-image-preview <?= empty($row['image']) ? 'is-empty' : '' ?>" id="slider-preview">
      <?php if (! empty($row['image'])): ?>
        <img id="slider-preview-image" src="<?= base_url('uploads/sliders/' . rawurlencode(basename($row['image']))) ?>" alt="Current slider image">
        <span id="slider-preview-placeholder" hidden>Banner preview</span>
      <?php else: ?>
        <img id="slider-preview-image" src="" alt="Selected slider image" hidden>
        <span id="slider-preview-placeholder">Banner preview</span>
      <?php endif ?>
    </div>
    <label>Slider image<input id="slider-image-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" <?= isset($row) ? '' : 'required' ?>><small>Recommended size: 1672 × 941 px. JPG, PNG or WebP, maximum 5 MB.</small></label>
  </div>

  <button class="btn wide" type="submit">Save slide</button>
</form>

<script>
(() => {
  const input = document.getElementById('slider-image-input');
  const preview = document.getElementById('slider-preview');
  const image = document.getElementById('slider-preview-image');
  const placeholder = document.getElementById('slider-preview-placeholder');
  let objectUrl;
  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);
    image.src = objectUrl;
    image.hidden = false;
    placeholder.hidden = true;
    preview.classList.remove('is-empty');
  });
})();
</script>

<?= $this->endSection() ?>
