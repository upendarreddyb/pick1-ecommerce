<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="password-layout">
  <form class="form-card password-card" method="post" action="<?= base_url('admin/password') ?>">
    <?= csrf_field() ?>
    <p class="password-intro">Confirm your existing password, then choose a new password for your administrator account.</p>
    <label>Current password<input type="password" name="current_password" required autocomplete="current-password" autofocus></label>
    <label>New password<input type="password" name="new_password" required minlength="10" maxlength="72" autocomplete="new-password"><small>Use at least 10 characters.</small></label>
    <label>Confirm new password<input type="password" name="confirm_password" required minlength="10" maxlength="72" autocomplete="new-password"></label>
    <button class="btn" type="submit">Update password</button>
  </form>

  <aside class="panel password-tips">
    <h2>Keep it secure</h2>
    <p>A strong, unique password helps protect your store and customer orders.</p>
    <ul class="security-list">
      <li>Use a mix of words, numbers, and symbols.</li>
      <li>Do not reuse a password from another account.</li>
      <li>Store the password in a trusted password manager.</li>
    </ul>
  </aside>
</div>

<?= $this->endSection() ?>
