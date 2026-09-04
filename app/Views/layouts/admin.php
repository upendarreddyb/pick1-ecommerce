<?php $section = service('uri')->getSegment(2) ?: 'dashboard'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= esc($title) ?> · Pick1 Admin</title>
  <link rel="icon" href="data:,">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/admin-password.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/admin-product-form.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/admin-slider.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/admin-video-stories.css') ?>">
</head>
<body>
  <button class="admin-toggle" type="button" aria-label="Open navigation" aria-expanded="false"><span></span><span></span><span></span></button>
  <div class="admin-overlay"></div>
  <aside class="admin-nav">
    <div>
      <a class="admin-brand" href="<?= base_url('admin') ?>">Pick<span>1</span><small>ADMIN</small></a>
      <p class="nav-label">Workspace</p>
      <nav>
        <a class="<?= $section==='dashboard'?'active':'' ?>" href="<?= base_url('admin') ?>"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg><span>Overview</span></a>
        <a class="<?= $section==='products'?'active':'' ?>" href="<?= base_url('admin/products') ?>"><svg viewBox="0 0 24 24"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z"></path><path d="m4 7.5 8 4.5 8-4.5M12 12v9"></path></svg><span>Products</span></a>
        <a class="<?= $section==='categories'?'active':'' ?>" href="<?= base_url('admin/categories') ?>"><svg viewBox="0 0 24 24"><path d="M3 7h7l2 2h9v11H3V7Z"></path><path d="M3 7V4h7l2 3"></path></svg><span>Categories</span></a>
        <a class="<?= $section==='coupons'?'active':'' ?>" href="<?= base_url('admin/coupons') ?>"><svg viewBox="0 0 24 24"><path d="M4 7h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4V7Z"></path><path d="M12 8v8"></path></svg><span>Coupons</span></a>
        <a class="<?= $section==='sliders'?'active':'' ?>" href="<?= base_url('admin/sliders') ?>"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m6 16 4-4 3 3 2-2 3 3M8 9h.01"></path></svg><span>Homepage slider</span></a>
        <a class="<?= $section==='announcement'?'active':'' ?>" href="<?= base_url('admin/announcement') ?>"><svg viewBox="0 0 24 24"><path d="M4 11v2M7 8v8l10 3V5L7 8Z"></path><path d="M9 16l1 4h3"></path></svg><span>Header discount</span></a>
        <a class="<?= $section==='video-stories'?'active':'' ?>" href="<?= base_url('admin/video-stories') ?>"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m10 9 5 3-5 3V9Z"></path></svg><span>Video stories</span></a>
        <a class="<?= in_array($section, ['orders', 'order-notifications'], true)?'active':'' ?>" href="<?= base_url('admin/orders') ?>"><svg viewBox="0 0 24 24"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"></path><path d="M9 8h6M9 12h6"></path></svg><span>Orders</span></a>
        <a class="<?= $section==='payments'?'active':'' ?>" href="<?= base_url('admin/payments') ?>"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18M7 15h3"></path></svg><span>Payments</span></a>
        <a class="<?= $section==='reviews'?'active':'' ?>" href="<?= base_url('admin/reviews') ?>"><svg viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"></path></svg><span>Reviews</span></a>
        <a class="<?= $section==='contact-messages'?'active':'' ?>" href="<?= base_url('admin/contact-messages') ?>"><svg viewBox="0 0 24 24"><path d="M4 5h16v12H8l-4 4V5Z"></path><path d="M8 9h8M8 13h5"></path></svg><span>Contact messages</span></a>
      </nav>
    </div>
    <div class="nav-bottom"><a class="<?= $section==='password'?'active':'' ?>" href="<?= base_url('admin/password') ?>"><svg viewBox="0 0 24 24"><rect x="5" y="10" width="14" height="10" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3M12 14v2"></path></svg><span>Change password</span></a><a href="<?= base_url('/') ?>" target="_blank"><svg viewBox="0 0 24 24"><path d="M14 5h5v5M19 5l-8 8"></path><path d="M19 13v6H5V5h6"></path></svg><span>View storefront</span></a><a href="<?= base_url('admin/logout') ?>"><svg viewBox="0 0 24 24"><path d="M10 4H4v16h6M14 8l4 4-4 4M8 12h10"></path></svg><span>Sign out</span></a></div>
  </aside>

  <main class="admin-main">
    <header class="admin-topbar">
      <div><p class="breadcrumb">Pick1 / <?= esc(ucfirst($section)) ?></p><h1><?= esc($title) ?></h1></div>
      <div class="admin-profile"><span><?= strtoupper(substr((string)session('admin_name'),0,1)) ?></span><div><strong><?= esc(session('admin_name')) ?></strong><small>Administrator</small></div></div>
    </header>
    <?php if(session('message')||session('error')): ?><div class="notice <?= session('error')?'notice-error':'' ?>"><span><?= session('error')?'!':'✓' ?></span><?= esc(session('message')??session('error')) ?></div><?php endif ?>
    <?= $this->renderSection('content') ?>
  </main>
  <script>(()=>{const button=document.querySelector('.admin-toggle'),nav=document.querySelector('.admin-nav'),overlay=document.querySelector('.admin-overlay');const close=()=>{nav.classList.remove('open');overlay.classList.remove('open');button.setAttribute('aria-expanded','false')};button.addEventListener('click',()=>{const active=nav.classList.toggle('open');overlay.classList.toggle('open',active);button.setAttribute('aria-expanded',active)});overlay.addEventListener('click',close)})();</script>
</body>
</html>
