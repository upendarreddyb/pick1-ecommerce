<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="description" content="Pick1 premium flavored toothpicks made from natural birchwood.">
  <title><?= isset($title) && $title !== 'Pick1' ? esc($title) . ' · Pick1' : 'Pick1' ?></title>
  <link rel="icon" href="data:,">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <?php $storeFonts = 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap'; ?>
  <link rel="stylesheet" href="<?= $storeFonts ?>" media="print" onload="this.media='all'">
  <noscript><link rel="stylesheet" href="<?= $storeFonts ?>"></noscript>
  <?php if (! empty($heroPreload)): ?><link rel="preload" as="image" href="<?= esc($heroPreload) ?>" type="image/webp" fetchpriority="high"><?php endif ?>
  <link rel="stylesheet" href="<?= base_url('assets/css/store.css') ?>">
  <style><?= file_get_contents(FCPATH . 'assets/css/store-layout-v2.css') ?></style>
  <?= $this->renderSection('head') ?>
</head>
<body class="pick1-store">
  <?php $announcement = db_connect()->tableExists('announcements') ? (new \App\Models\AnnouncementModel())->where('status', 'active')->first() : null; ?>
  <?php if ($announcement): ?>
    <div class="discount-ticker" role="status" aria-label="Current offer" style="--ticker-speed:<?= max(8, min(60, (int) $announcement['speed'])) ?>s"><div><span><?= esc($announcement['message']) ?></span><span aria-hidden="true"><?= esc($announcement['message']) ?></span><span aria-hidden="true"><?= esc($announcement['message']) ?></span><span aria-hidden="true"><?= esc($announcement['message']) ?></span></div></div>
  <?php endif ?>
  <header class="site-header pick-nav">
    <a class="header-logo" href="<?= base_url('/') ?>" aria-label="Pick1 home">
      <img src="<?= base_url('assets/images/pick1-logo.webp') ?>" alt="Pick1 Premium Flavoured Toothpicks" width="360" height="186" decoding="async">
    </a>
    <button class="menu-toggle" aria-label="Open menu" aria-expanded="false">☰</button>
    <nav aria-label="Main navigation">
      <a href="<?= base_url('/') ?>">Home</a>
      <a href="<?= base_url('about') ?>">About</a>
      <a href="<?= base_url('products') ?>">Shop</a>
      <a href="<?= base_url('bulk-orders') ?>">Bulk Orders</a>
      <a href="<?= base_url('contact') ?>">Contact Us</a>
    </nav>
    <div class="header-actions">
      <form class="header-search-form" action="<?= base_url('products') ?>" method="get" role="search">
        <label class="sr-only" for="header-product-search">Search products</label>
        <input id="header-product-search" name="q" type="search" value="<?= esc((string) service('request')->getGet('q')) ?>" placeholder="Search">
        <button class="header-icon search-icon" type="submit" aria-label="Search products"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"></circle><path d="m16 16 5 5"></path></svg></button>
      </form>
      <?php if(session('customer_id')): ?><details class="account-menu"><summary class="header-icon account-icon" aria-label="Open account menu"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.2" r="4.2"></circle><path d="M4.5 21c.4-5 3-7.4 7.5-7.4s7.1 2.4 7.5 7.4"></path></svg></summary><div class="account-dropdown"><small><?= esc(session('customer_email')) ?></small><a href="<?= base_url('orders') ?>">My orders</a><form method="post" action="<?= base_url('logout') ?>"><?= csrf_field() ?><button type="submit">Log out</button></form></div></details><?php else: ?><a class="header-icon account-icon" href="<?= base_url('login') ?>" aria-label="Log in"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="7.2" r="4.2"></circle><path d="M4.5 21c.4-5 3-7.4 7.5-7.4s7.1 2.4 7.5 7.4"></path></svg></a><?php endif ?>
      <a class="cart-trigger header-icon" href="<?= base_url('cart') ?>" aria-label="View shopping cart"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.5 8.5h13l-1 12h-11l-1-12Z"></path><path d="M9 9V6.3a3 3 0 0 1 6 0V9"></path></svg><b data-cart-count><?= (new \App\Libraries\Cart())->count() ?></b></a>
    </div>
  </header>
  <?php if (session('message') || session('error')): ?><div class="flash <?= session('error') ? 'error' : '' ?>"><?= esc(session('error') ?? session('message')) ?></div><?php endif ?>
  <main><?= $this->renderSection('content') ?></main>
  <footer class="numae-footer premium-footer">
    <section class="footer-signup">
      <nav class="footer-socials" aria-label="Social media">
        <a href="<?= esc(env('social.facebookUrl', '#')) ?>" aria-label="Facebook" <?= env('social.facebookUrl') ? 'target="_blank" rel="noopener"' : '' ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h4V3h-4c-4 0-6 2.5-6 6v3H4v5h4v7h5v-7h4l1-5h-5V9c0-.7.3-1 1-1Z"/></svg></a>
        <a href="<?= esc(env('social.instagramUrl', 'https://www.instagram.com/pick1toothpicks?igsh=MWY2OXNxdzV2bWZsdg%3D%3D')) ?>" aria-label="Instagram" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4.2"/><circle cx="17.5" cy="6.5" r="1" class="fill"/></svg></a>
        <a href="<?= esc(env('social.youtubeUrl', '#')) ?>" aria-label="YouTube" <?= env('social.youtubeUrl') ? 'target="_blank" rel="noopener"' : '' ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 7.2a3 3 0 0 0-2.1-2.1C17 4.5 12 4.5 12 4.5s-5 0-6.9.6A3 3 0 0 0 3 7.2c-.5 1.9-.5 4.8-.5 4.8s0 2.9.5 4.8a3 3 0 0 0 2.1 2.1c1.9.6 6.9.6 6.9.6s5 0 6.9-.6a3 3 0 0 0 2.1-2.1c.5-1.9.5-4.8.5-4.8s0-2.9-.5-4.8Z"/><path class="fill" d="m10 15.5 5-3.5-5-3.5v7Z"/></svg></a>
        <a href="<?= esc(env('social.whatsappUrl', '#')) ?>" aria-label="WhatsApp" <?= env('social.whatsappUrl') ? 'target="_blank" rel="noopener"' : '' ?>><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.5 11.7A8.5 8.5 0 0 1 8 19.2L3.5 20.5 4.8 16A8.5 8.5 0 1 1 20.5 11.7Z"/><path d="M8.3 7.5c.3-.3.6-.3.9-.1l1 2c.1.2 0 .5-.2.7l-.6.6c.7 1.5 1.8 2.6 3.3 3.3l.7-.8c.2-.2.5-.2.7-.1l1.8.9c.3.2.4.5.3.8-.3 1.2-1.4 1.8-2.5 1.7-3.8-.4-6.9-3.5-7.3-7.3-.1-.7.7-1.4 1.9-1.7Z"/></svg></a>
      </nav>
      <h2>Join The Pick1</h2>
      <p>Sign up for exclusive content, special prices, and the latest updates.</p>
      <form onsubmit="event.preventDefault()">
        <label class="sr-only" for="footer-email">Email</label>
        <input id="footer-email" type="email" placeholder="Enter Your Email" required>
        <button type="submit">Subscribe</button>
      </form>
    </section>

    <section class="footer-directory">
      <div>
        <a class="footer-logo" href="<?= base_url('/') ?>" aria-label="Pick1 home"><img src="<?= base_url('assets/images/pick1-logo.webp') ?>" alt="Pick1 Premium Flavoured Toothpicks" width="360" height="186" loading="lazy" decoding="async"></a>
        <h3>About</h3>
        <a href="<?= base_url('/#why-pick1-title') ?>">Why Pick1?</a>
        <a href="<?= base_url('contact') ?>">Contact Us</a>
      </div>
      <div>
        <h3>Discover</h3>
        <a href="<?= base_url('products') ?>">Shop</a>
        <a href="<?= base_url('bulk-orders') ?>">Bulk Orders</a>
      </div>
      <div>
        <h3>Help</h3>
        <a href="<?= base_url('policies#privacy-policy') ?>">Privacy Policy</a>
        <a href="<?= base_url('policies#shipping-policy') ?>">Shipping Policy</a>
        <a href="<?= base_url('policies#return-refund-policy') ?>">Returns &amp; Refunds</a>
        <a href="<?= base_url('policies#terms-of-service') ?>">Terms &amp; Conditions</a>
        <a href="<?= base_url('disclaimer') ?>">Disclaimer</a>
      </div>
      <div class="footer-contact">
        <h3>Contact Us</h3>
        <a href="tel:+919703255444">+91 97032 55444</a>
        <a href="mailto:support@pick1.in">support@pick1.in</a>
        <a href="mailto:info@pick1.in">info@pick1.in</a>
      </div>
    </section>

    <section class="footer-bottom">
      <small>© <?= date('Y') ?> Pick1. All rights reserved.</small>
    </section>
  </footer>
  <div class="toast" role="status"></div>
  <script>window.CSRF={name:'<?= csrf_token() ?>',hash:'<?= csrf_hash() ?>'};</script>
  <script src="<?= base_url('assets/js/store.js') ?>"></script>
  <?= $this->renderSection('scripts') ?>
</body>
</html>
