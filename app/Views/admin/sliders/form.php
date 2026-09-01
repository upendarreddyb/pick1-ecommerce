<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<form class="form-card slider-form" method="post" enctype="multipart/form-data" action="<?= isset($row) ? base_url('admin/sliders/' . $row['id']) : base_url('admin/sliders') ?>">
  <?= csrf_field() ?>
  <?php if (isset($row)): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>

  <label>Small heading <small>(optional)</small><input name="eyebrow" maxlength="120" value="<?= esc(old('eyebrow', $row['eyebrow'] ?? '')) ?>" placeholder="Natural everyday ritual"></label>
  <label>Display order <small>(optional)</small><input type="number" name="sort_order" value="<?= esc(old('sort_order', $row['sort_order'] ?? '')) ?>" placeholder="0"></label>
  <label class="wide">Main heading <small>(optional)</small><input name="title" maxlength="180" value="<?= esc(old('title', $row['title'] ?? '')) ?>" placeholder="Naturally fresh"></label>
  <label class="wide">Description <small>(optional)</small><textarea name="description" maxlength="300" rows="3" placeholder="Premium flavored toothpicks, thoughtfully made."><?= esc(old('description', $row['description'] ?? '')) ?></textarea></label>
  <label>Button text <small>(optional)</small><input name="button_text" maxlength="80" value="<?= esc(old('button_text', $row['button_text'] ?? '')) ?>" placeholder="Shop Now"></label>
  <label>Button link <small>(optional)</small><input name="button_url" maxlength="255" value="<?= esc(old('button_url', $row['button_url'] ?? '')) ?>" placeholder="products"></label>
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
    <label>Slider image <small>(required)</small><input id="slider-image-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required><small>Upload an image whenever you add or edit a slide. Recommended size: 1672 × 941 px. JPG, PNG or WebP, maximum 5 MB.</small></label>
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
