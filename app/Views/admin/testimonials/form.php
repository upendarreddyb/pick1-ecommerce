<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<form class="form-card slider-form video-story-form" method="post" enctype="multipart/form-data" action="<?= isset($row) ? base_url('admin/video-stories/' . $row['id']) : base_url('admin/video-stories') ?>">
  <?= csrf_field() ?>
  <?php if (isset($row)): ?><input type="hidden" name="_method" value="PUT"><?php endif ?>

  <label>Customer name<input name="customer_name" maxlength="120" value="<?= esc(old('customer_name', $row['customer_name'] ?? '')) ?>" required placeholder="Customer name"></label>
  <label>Story title<input name="title" maxlength="180" value="<?= esc(old('title', $row['title'] ?? '')) ?>" placeholder="A refreshing everyday ritual"></label>
  <label>Rating<select name="rating"><?php for ($rating = 5; $rating >= 1; $rating--): ?><option value="<?= $rating ?>" <?= (int) old('rating', $row['rating'] ?? 5) === $rating ? 'selected' : '' ?>><?= $rating ?> star<?= $rating === 1 ? '' : 's' ?></option><?php endfor ?></select></label>
  <label>Display order<input type="number" name="sort_order" value="<?= esc(old('sort_order', $row['sort_order'] ?? 0)) ?>" required></label>
  <label>Status<select name="status"><option value="active" <?= old('status', $row['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= old('status', $row['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label>
  <label class="wide">Customer review<textarea name="review" maxlength="2000" rows="5" placeholder="What did the customer say about Pick1?"><?= esc(old('review', $row['review'] ?? '')) ?></textarea></label>
  <label class="wide">YouTube or Instagram link<input type="url" name="external_url" maxlength="500" value="<?= esc(old('external_url', $row['external_url'] ?? '')) ?>" placeholder="https://www.youtube.com/watch?v=... or https://www.instagram.com/reel/..."><small>Paste a public YouTube video, YouTube Shorts, Instagram Reel or Instagram post link. Leave empty when uploading a file.</small></label>

  <?php if (! empty($row['video'])): ?>
    <div class="wide current-video"><p>Current video</p><video controls preload="metadata" <?= ! empty($row['poster']) ? 'poster="' . esc(base_url('uploads/video-stories/' . rawurlencode(basename($row['poster'])))) . '"' : '' ?>><source src="<?= esc(base_url('uploads/video-stories/' . rawurlencode(basename($row['video'])))) ?>"></video></div>
  <?php elseif (! empty($row['external_url'])): ?>
    <div class="wide current-video"><p>Current <?= esc(ucfirst($row['provider'] ?? 'linked')) ?> video</p><a href="<?= esc($row['external_url']) ?>" target="_blank" rel="noopener">Open video link ↗</a></div>
  <?php endif ?>

  <label class="wide">Or upload customer video<input type="file" name="video" accept=".mp4,.webm,.mov,video/mp4,video/webm,video/quicktime"><small>MP4, WebM or MOV, maximum 50 MB. An uploaded file replaces the linked video.</small></label>
  <label class="wide">Poster image (optional)<input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><small>Shown before the video starts. JPG, PNG or WebP, maximum 5 MB.</small></label>

  <button class="btn wide" type="submit">Save video story</button>
</form>

<?= $this->endSection() ?>
