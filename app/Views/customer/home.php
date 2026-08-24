<?= $this->extend('layouts/store') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/home-carousel.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/home-spacing.css?v=2') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$displaySlides = $slides ?: [
    ['default' => true, 'image' => 'assets/images/pick1-mint-hero.png', 'eyebrow' => 'Natural everyday ritual', 'title' => 'Naturally fresh', 'description' => 'Premium flavored toothpicks, thoughtfully made.', 'button_text' => 'Shop Now', 'button_url' => 'products'],
    ['default' => true, 'image' => 'assets/images/pick1-cinnamon-hero.png', 'eyebrow' => 'Warm & aromatic', 'title' => 'Cinnamon comfort', 'description' => 'A rich flavor for calm, mindful moments.', 'button_text' => 'Explore Flavors', 'button_url' => 'products'],
    ['default' => true, 'image' => 'assets/images/pick1-fresh-mint-hero.png', 'eyebrow' => 'Clean & uplifting', 'title' => 'Freshness, refined', 'description' => 'Carry a naturally fresh feeling wherever you go.', 'button_text' => 'Shop Mint', 'button_url' => 'products'],
];
$fallbackSlideImages = [
    'assets/images/pick1-mint-hero.png',
    'assets/images/pick1-cinnamon-hero.png',
    'assets/images/pick1-fresh-mint-hero.png',
];
?>

<section class="numae-hero home-carousel" aria-roledescription="carousel" aria-label="Pick1 featured collections">
  <div class="carousel-slides" aria-live="off">
    <?php foreach ($displaySlides as $index => $slide): ?>
      <?php
      $sliderFilename = basename((string) ($slide['image'] ?? ''));
      $hasUploadedImage = $sliderFilename !== '' && is_file(FCPATH . 'uploads/sliders/' . $sliderFilename);
      $imageUrl = ! empty($slide['default'])
          ? base_url($slide['image'])
          : ($hasUploadedImage
              ? base_url('uploads/sliders/' . rawurlencode($sliderFilename))
              : base_url($fallbackSlideImages[$index % count($fallbackSlideImages)]));
      $buttonUrl = preg_match('#^https?://#i', (string) ($slide['button_url'] ?? ''))
          ? $slide['button_url']
          : base_url(ltrim((string) ($slide['button_url'] ?? 'products'), '/'));
      ?>
      <article class="carousel-slide <?= $index === 0 ? 'is-active' : '' ?>" data-slide aria-hidden="<?= $index === 0 ? 'false' : 'true' ?>">
        <img src="<?= esc($imageUrl) ?>" alt="<?= esc($slide['title']) ?>" <?= $index === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
        <div class="numae-hero-copy">
          <?php if (! empty($slide['eyebrow'])): ?><span><?= esc($slide['eyebrow']) ?></span><?php endif ?>
          <?php if ($index === 0): ?><h1><?= esc($slide['title']) ?></h1><?php else: ?><h2><?= esc($slide['title']) ?></h2><?php endif ?>
          <?php if (! empty($slide['description'])): ?><p><?= esc($slide['description']) ?></p><?php endif ?>
          <?php if (! empty($slide['button_text'])): ?><a href="<?= esc($buttonUrl) ?>"><?= esc($slide['button_text']) ?></a><?php endif ?>
        </div>
      </article>
    <?php endforeach ?>
  </div>

  <?php if (count($displaySlides) > 1): ?>
    <button class="carousel-arrow carousel-prev" type="button" aria-label="Previous slide">‹</button>
    <button class="carousel-arrow carousel-next" type="button" aria-label="Next slide">›</button>
    <div class="numae-dots" role="group" aria-label="Choose a slide">
      <?php foreach ($displaySlides as $index => $_slide): ?><button class="<?= $index === 0 ? 'is-active' : '' ?>" type="button" aria-label="Show slide <?= $index + 1 ?>" <?= $index === 0 ? 'aria-current="true"' : '' ?>></button><?php endforeach ?>
    </div>
  <?php endif ?>
</section>

<section class="pick1-benefit-marquee" aria-label="Why choose Pick1">
  <div class="pick1-benefit-track">
    <?php for ($loop = 0; $loop < 2; $loop++): ?>
      <div class="pick1-benefit-group" <?= $loop === 1 ? 'aria-hidden="true"' : '' ?>>
        <span>🌿 <strong>Natural Wood</strong></span>
        <span>🍃 <strong>Refreshing Flavours</strong></span>
        <span>✨ <strong>Premium Quality</strong></span>
        <span>🦷 <strong>Food Grade</strong></span>
        <span>📦 <strong>Hygienically Packed</strong></span>
        <span>🇮🇳 <strong>Made in India</strong></span>
      </div>
    <?php endfor ?>
  </div>
</section>

<section class="numae-intro"><h2>Natural Ingredients</h2><p>crafted from natural birchwood and natural flavors<br>for a health-focused toothpick</p></section>

<section class="numae-products"><header><p>Featured Products</p><em>pure, premium, and always loved</em></header><div class="product-grid"><?php foreach ($products as $p): ?><?= view('customer/products/_card', ['p' => $p, 'cartQuantity' => $cartQuantities[(int) $p['id']] ?? 0]) ?><?php endforeach ?></div><a class="numae-shop" href="<?= base_url('products') ?>">Shop all products</a></section>

<section class="pick1-faq" aria-labelledby="pick1-faq-title">
  <header>
    <p class="pick-eyebrow">Everything you need to know</p>
    <h2 id="pick1-faq-title">Flavored Toothpicks</h2>
  </header>
  <div class="pick1-faq-list">
    <details open>
      <summary>What is Pick1?</summary>
      <div><p>Pick1 is a premium flavored toothpick designed to give you a refreshing, satisfying experience anytime, anywhere. It’s a simple alternative to chewing gum, smoking, or constantly snacking.</p></div>
    </details>
    <details>
      <summary>Why should I use Pick1?</summary>
      <div><p>Pick1 helps keep your mouth feeling fresh while giving you something enjoyable to chew on. Whether you’re working, driving, studying, or relaxing, it’s an easy way to satisfy the urge to chew without gum or sugary mints.</p></div>
    </details>
    <details>
      <summary>How long does the flavor last?</summary>
      <div><p>The flavor typically lasts <strong>10–20 minutes</strong>, depending on how you use the toothpick. Some people enjoy the taste even longer with gentle chewing.</p></div>
    </details>
    <details>
      <summary>Does the wood have any taste?</summary>
      <div><p>No. Pick1 is made from carefully selected, food-grade birch wood with a naturally neutral taste, allowing you to enjoy only the infused flavor.</p></div>
    </details>
    <details>
      <summary>Are the flavors very strong?</summary>
      <div><p>Pick1 is crafted to deliver a balanced flavor experience.</p><ul><li><strong>Mint:</strong> Cool, refreshing, and crisp.</li><li><strong>Cinnamon:</strong> Warm with a gentle spicy kick.</li><li>Other flavors are designed to be smooth, enjoyable, and long-lasting.</li></ul></div>
    </details>
    <details>
      <summary>Is Pick1 biodegradable?</summary>
      <div><p>Yes. Our wooden toothpicks are biodegradable, making them a better choice than many disposable plastic products.</p></div>
    </details>
    <details>
      <summary>Is Pick1 vegan?</summary>
      <div><p>Yes. Pick1 contains no animal-derived ingredients and is made using plant-based flavoring ingredients.</p></div>
    </details>
    <details>
      <summary>How many Pick1 toothpicks can I use in a day?</summary>
      <div><p>Most people enjoy <strong>5–10 toothpicks per day</strong>. Since our flavors are concentrated, we recommend enjoying them in moderation.</p></div>
    </details>
    <details>
      <summary>Can I use the same toothpick again?</summary>
      <div><p>Yes. You may reuse the same Pick1 toothpick until the flavor fades. For hygiene, store it in a clean place between uses and discard it once it becomes worn or loses its flavor.</p></div>
    </details>
    <details>
      <summary>Is Pick1 better than chewing gum?</summary>
      <div><p>Pick1 offers a different experience. Unlike chewing gum, Pick1 contains no chewing base, creates no sticky waste, and provides a clean, refreshing flavor in a compact, portable form.</p></div>
    </details>
    <details>
      <summary>What is Pick1 made from?</summary>
      <div><p>Pick1 is made using:</p><ul><li>Premium food-grade birch wood</li><li>Carefully selected food-grade flavouring ingredients</li><li>Natural sweetener (Stevia), where applicable</li><li>Food-safe processing methods</li></ul><p><strong>No plastic. No artificial chewing base.</strong> Just quality ingredients for a refreshing experience.</p></div>
    </details>
    <details>
      <summary>Is Pick1 safe?</summary>
      <div><p>Yes. Pick1 is manufactured under strict quality standards using food-grade materials. Use only as directed, avoid swallowing the toothpick, and keep out of reach of young children.</p></div>
    </details>
    <details>
      <summary>What flavors are available?</summary>
      <div><p>Pick1 is available in a variety of exciting flavors, including:</p><ul><li>Mint</li><li>Cinnamon</li><li>Cardamom</li><li>Paan Masala</li><li>Clove</li></ul><p>More exciting flavors are coming soon.</p></div>
    </details>
    <!-- <details>
      <summary>Can Pick1 replace smoking or chewing tobacco?</summary>
      <div><p>Pick1 is not a medical product or smoking-cessation treatment. However, many customers enjoy it as a flavorful alternative to keep their mouth occupied during daily routines.</p></div>
    </details> -->
    <details class="pick1-promise">
      <summary>Pick1 Promise</summary>
      <div><p><strong>Fresh Flavor. Premium Quality. Anytime Refreshment.</strong></p><p>Experience a smarter way to refresh your breath—one toothpick at a time.</p></div>
    </details>
  </div>
</section>

<?php if (! empty($videoStories)): ?>
<section class="video-stories" aria-labelledby="video-stories-title">
  <header>
    <div class="video-stories-rating" aria-label="Five star customer reviews">
      <span aria-hidden="true">★★★★★</span>
      <strong><?= count($videoStories) ?> customer stories</strong>
    </div>
    <h2 id="video-stories-title">Freshness They’ve Experienced.</h2>
  </header>

  <div class="video-stories-track" data-video-stories>
    <?php foreach ($videoStories as $story): ?>
      <?php
      $videoUrl = ! empty($story['video']) ? base_url('uploads/video-stories/' . rawurlencode(basename($story['video']))) : '';
      $posterUrl = ! empty($story['poster'])
          ? base_url('uploads/video-stories/' . rawurlencode(basename($story['poster'])))
          : '';
      $embedUrl = '';
      if (($story['provider'] ?? '') === 'youtube' && ! empty($story['external_url'])) {
          $parts = parse_url($story['external_url']);
          $host = strtolower((string) ($parts['host'] ?? ''));
          $path = trim((string) ($parts['path'] ?? ''), '/');
          parse_str((string) ($parts['query'] ?? ''), $query);
          $youtubeId = str_contains($host, 'youtu.be') ? explode('/', $path)[0] : ($query['v'] ?? '');
          if ($youtubeId === '' && preg_match('#^(?:shorts|embed)/([^/]+)#', $path, $match)) {
              $youtubeId = $match[1];
          }
          $youtubeId = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $youtubeId);
          if ($youtubeId !== '') {
              $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $youtubeId . '?autoplay=1&rel=0';
              $posterUrl = $posterUrl ?: 'https://i.ytimg.com/vi/' . $youtubeId . '/hqdefault.jpg';
          }
      } elseif (($story['provider'] ?? '') === 'instagram' && ! empty($story['external_url'])) {
          $parts = parse_url($story['external_url']);
          $path = (string) ($parts['path'] ?? '');
          if (preg_match('#^/(?:reel|p|tv)/[A-Za-z0-9_-]+#', $path, $match)) {
              $embedUrl = 'https://www.instagram.com' . rtrim($match[0], '/') . '/embed/';
          }
      }
      $rating = max(1, min(5, (int) $story['rating']));
      ?>
      <article class="video-story-card">
        <button class="video-story-media" type="button" <?= $embedUrl ? 'data-embed="' . esc($embedUrl) . '"' : 'data-video="' . esc($videoUrl) . '"' ?> aria-label="Play <?= esc($story['customer_name']) ?>’s video">
          <?php if ($posterUrl): ?>
            <img src="<?= esc($posterUrl) ?>" alt="" loading="lazy">
          <?php elseif ($videoUrl): ?>
            <video src="<?= esc($videoUrl) ?>#t=0.1" muted preload="metadata" aria-hidden="true"></video>
          <?php else: ?>
            <span class="video-story-provider"><?= esc(ucfirst($story['provider'] ?? 'Video')) ?></span>
          <?php endif ?>
          <span class="video-story-shade"></span>
          <span class="video-story-play" aria-hidden="true">▶</span>
        </button>
        <h3><?= esc($story['customer_name']) ?></h3>
        <?php if (! empty($story['title'])): ?><p class="video-story-title"><?= esc($story['title']) ?></p><?php endif ?>
        <div class="video-story-stars" aria-label="<?= $rating ?> out of 5 stars"><?= str_repeat('★', $rating) ?><span><?= str_repeat('★', 5 - $rating) ?></span></div>
        <?php if (! empty($story['review'])): ?><blockquote>“<?= esc($story['review']) ?>”</blockquote><?php endif ?>
      </article>
    <?php endforeach ?>
  </div>
</section>

<dialog class="video-story-dialog" id="video-story-dialog" aria-label="Customer video">
  <button type="button" class="video-story-close" aria-label="Close video">×</button>
  <video controls playsinline></video>
  <iframe title="Customer video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
</dialog>
<?php endif ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
  const carousel = document.querySelector('.home-carousel');
  if (!carousel) return;

  const slides = [...carousel.querySelectorAll('[data-slide]')];
  const dots = [...carousel.querySelectorAll('.numae-dots button')];
  const previous = carousel.querySelector('.carousel-prev');
  const next = carousel.querySelector('.carousel-next');
  if (slides.length < 2 || !previous || !next) return;

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let active = 0;
  let timer;
  let touchStart = 0;

  const show = (index) => {
    active = (index + slides.length) % slides.length;
    slides.forEach((slide, position) => {
      const selected = position === active;
      slide.classList.toggle('is-active', selected);
      slide.setAttribute('aria-hidden', selected ? 'false' : 'true');
      slide.inert = !selected;
    });
    dots.forEach((dot, position) => {
      const selected = position === active;
      dot.classList.toggle('is-active', selected);
      if (selected) dot.setAttribute('aria-current', 'true');
      else dot.removeAttribute('aria-current');
    });
  };

  const stop = () => window.clearInterval(timer);
  const start = () => {
    stop();
    if (!reducedMotion) timer = window.setInterval(() => show(active + 1), 5500);
  };

  previous.addEventListener('click', () => { show(active - 1); start(); });
  next.addEventListener('click', () => { show(active + 1); start(); });
  dots.forEach((dot, index) => dot.addEventListener('click', () => { show(index); start(); }));
  carousel.addEventListener('mouseenter', stop);
  carousel.addEventListener('mouseleave', start);
  carousel.addEventListener('focusin', stop);
  carousel.addEventListener('focusout', start);
  carousel.addEventListener('touchstart', event => { touchStart = event.changedTouches[0].clientX; }, {passive: true});
  carousel.addEventListener('touchend', event => {
    const distance = event.changedTouches[0].clientX - touchStart;
    if (Math.abs(distance) > 45) show(active + (distance < 0 ? 1 : -1));
    start();
  }, {passive: true});
  show(0);
  start();
})();

(() => {
  const dialog = document.getElementById('video-story-dialog');
  if (!dialog) return;
  const player = dialog.querySelector('video');
  const frame = dialog.querySelector('iframe');
  const close = () => {
    player.pause();
    player.removeAttribute('src');
    player.load();
    frame.removeAttribute('src');
    frame.hidden = true;
    player.hidden = false;
    dialog.close();
  };
  document.querySelectorAll('[data-video], [data-embed]').forEach(button => {
    button.addEventListener('click', () => {
      if (button.dataset.embed) {
        player.hidden = true;
        frame.hidden = false;
        frame.src = button.dataset.embed;
      } else {
        frame.hidden = true;
        player.hidden = false;
        player.src = button.dataset.video;
      }
      dialog.showModal();
      if (button.dataset.video) player.play().catch(() => {});
    });
  });
  dialog.querySelector('.video-story-close').addEventListener('click', close);
  dialog.addEventListener('click', event => {
    if (event.target === dialog) close();
  });
  dialog.addEventListener('cancel', event => {
    event.preventDefault();
    close();
  });
})();
</script>
<?= $this->endSection() ?>
