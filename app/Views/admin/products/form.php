<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<form class="form-card" method="post" enctype="multipart/form-data" action="<?= isset($row) ? base_url('admin/products/' . $row['id']) : base_url('admin/products') ?>">
  <?= csrf_field() ?>
  <?php if (isset($row)): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>

  <label>Product name<input name="name" value="<?= esc(old('name', $row['name'] ?? '')) ?>" required></label>
  <label>Category<select name="category_id"><?php foreach ($categories as $category): ?><option value="<?= $category['id'] ?>" <?= ($row['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>><?= esc($category['name']) ?></option><?php endforeach ?></select></label>
  <label>Regular price<input type="number" step=".01" name="price" value="<?= esc(old('price', $row['price'] ?? '')) ?>" required></label>
  <label>Sale price<input type="number" step=".01" name="sale_price" value="<?= esc(old('sale_price', $row['sale_price'] ?? '')) ?>"></label>
  <label>Stock<input type="number" min="0" name="stock" value="<?= esc(old('stock', $row['stock'] ?? 0)) ?>" required></label>
  <label>Status<select name="status"><option value="active">Active</option><option value="inactive" <?= ($row['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label>
  <label class="wide">Description<textarea name="description" rows="6"><?= esc(old('description', $row['description'] ?? '')) ?></textarea></label>

  <div class="wide product-image-field">
    <div class="field-heading">
      <div><strong>Product image</strong><small><?= ! empty($row['image']) ? 'Current image' : 'No image uploaded yet' ?></small></div>
      <?php if (! empty($row['image'])): ?><span class="image-status">Uploaded</span><?php endif ?>
    </div>

    <div class="product-image-editor">
      <div class="product-image-preview <?= empty($row['image']) ? 'is-empty' : '' ?>" id="product-image-preview">
        <?php if (! empty($row['image'])): ?>
          <img id="product-preview-image" src="<?= base_url('uploads/products/' . rawurlencode(basename($row['image']))) ?>" alt="<?= esc($row['name'] ?? 'Product') ?> image">
          <span id="product-preview-placeholder" hidden>No image</span>
        <?php else: ?>
          <img id="product-preview-image" src="" alt="Selected product image" hidden>
          <span id="product-preview-placeholder">No image</span>
        <?php endif ?>
      </div>

      <label class="product-image-upload">
        <span><?= ! empty($row['image']) ? 'Replace image' : 'Upload image' ?></span>
        <input id="product-image-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
        <small>JPG, PNG or WebP. Maximum 2 MB. The preview updates before you save.</small>
        <em id="selected-image-name"><?= ! empty($row['image']) ? esc(basename($row['image'])) : 'No file selected' ?></em>
      </label>
    </div>
  </div>

  <div class="wide product-gallery-field">
    <div class="field-heading">
      <div><strong>Additional product images</strong><small>Add up to four images customers can select on the product page.</small></div>
      <span class="gallery-count"><b id="gallery-count"><?= count($gallery ?? []) ?></b>/4</span>
    </div>

    <?php if (! empty($gallery)): ?>
      <div class="existing-gallery">
        <?php foreach ($gallery as $galleryImage): ?>
          <label class="existing-gallery-item">
            <img src="<?= base_url('uploads/products/' . rawurlencode(basename($galleryImage['image']))) ?>" alt="Additional product image">
            <span><input class="remove-gallery-input" type="checkbox" name="remove_gallery[]" value="<?= (int) $galleryImage['id'] ?>"> Remove</span>
          </label>
        <?php endforeach ?>
      </div>
    <?php endif ?>

    <label class="gallery-upload">
      <span>Upload additional images</span>
      <input id="product-gallery-input" type="file" name="gallery_images[]" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" multiple>
      <small>Choose up to four JPG, PNG or WebP files. Maximum 2 MB per image.</small>
    </label>
    <div class="new-gallery-preview" id="new-gallery-preview" aria-live="polite"></div>
  </div>

  <button class="btn wide">Save product</button>
</form>

<script>
(() => {
  const input = document.getElementById('product-image-input');
  const preview = document.getElementById('product-image-preview');
  const image = document.getElementById('product-preview-image');
  const placeholder = document.getElementById('product-preview-placeholder');
  const filename = document.getElementById('selected-image-name');
  let temporaryUrl;

  input.addEventListener('change', () => {
    const file = input.files[0];
    if (!file) return;
    if (temporaryUrl) URL.revokeObjectURL(temporaryUrl);
    temporaryUrl = URL.createObjectURL(file);
    image.src = temporaryUrl;
    image.hidden = false;
    placeholder.hidden = true;
    preview.classList.remove('is-empty');
    filename.textContent = file.name;
  });

  const galleryInput = document.getElementById('product-gallery-input');
  const galleryPreview = document.getElementById('new-gallery-preview');
  const galleryCount = document.getElementById('gallery-count');
  const removeInputs = [...document.querySelectorAll('.remove-gallery-input')];
  const existingCount = removeInputs.length;
  let galleryUrls = [];

  const updateGalleryCount = () => {
    const removed = removeInputs.filter(input => input.checked).length;
    const selected = galleryInput.files ? galleryInput.files.length : 0;
    galleryCount.textContent = existingCount - removed + selected;
  };

  removeInputs.forEach(input => input.addEventListener('change', () => {
    input.closest('.existing-gallery-item').classList.toggle('will-remove', input.checked);
    updateGalleryCount();
  }));

  galleryInput.addEventListener('change', () => {
    galleryUrls.forEach(url => URL.revokeObjectURL(url));
    galleryUrls = [];
    galleryPreview.replaceChildren();

    const removed = removeInputs.filter(input => input.checked).length;
    const availableSlots = 4 - (existingCount - removed);
    if (galleryInput.files.length > availableSlots) {
      galleryInput.value = '';
      galleryPreview.textContent = `You can select ${availableSlots} more image${availableSlots === 1 ? '' : 's'}.`;
      updateGalleryCount();
      return;
    }

    [...galleryInput.files].forEach(file => {
      const url = URL.createObjectURL(file);
      galleryUrls.push(url);
      const item = document.createElement('figure');
      const image = document.createElement('img');
      const caption = document.createElement('figcaption');
      image.src = url;
      image.alt = '';
      caption.textContent = file.name;
      item.append(image, caption);
      galleryPreview.append(item);
    });
    updateGalleryCount();
  });
})();
</script>

<?= $this->endSection() ?>
