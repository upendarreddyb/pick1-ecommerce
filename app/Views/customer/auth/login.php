<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<section class="auth-card">
  <p class="eyebrow">Welcome</p>
  <h1>Sign in, simply.</h1>
  <p>No password to remember. We’ll email you a one-time code.</p>
  <form method="post" data-login-form>
    <?= csrf_field() ?>
    <label>Email address<input type="email" name="email" value="<?= old('email') ?>" required autocomplete="email" placeholder="you@example.com"></label>
    <button type="submit" class="button dark" data-login-submit><span>Send my code</span></button>
  </form>
</section>
<script>
(()=>{
  const form=document.querySelector('[data-login-form]');
  const button=form?.querySelector('[data-login-submit]');
  if(!form||!button)return;
  form.addEventListener('submit',event=>{
    if(form.dataset.submitting==='true'){
      event.preventDefault();
      return;
    }
    if(!form.checkValidity())return;
    form.dataset.submitting='true';
    form.classList.add('is-submitting');
    button.disabled=true;
    button.setAttribute('aria-busy','true');
    button.querySelector('span').textContent='Sending code…';
  });
})();
</script>
<?= $this->endSection() ?>
