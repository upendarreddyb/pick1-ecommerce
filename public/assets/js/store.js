(() => {
  const drawer = document.querySelector('.cart-drawer');
  const scrim = document.querySelector('.scrim');
  const toast = document.querySelector('.toast');
  const openDrawer = () => { drawer?.classList.add('open'); scrim?.classList.add('open'); drawer?.setAttribute('aria-hidden', 'false'); };
  const closeDrawer = () => { drawer?.classList.remove('open'); scrim?.classList.remove('open'); drawer?.setAttribute('aria-hidden', 'true'); };
  const notify = message => { if (!toast) return; toast.textContent = message; toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 1800); };
  const syncResponse = payload => {
    if (payload.csrfHash) CSRF.hash = payload.csrfHash;
    document.querySelectorAll('[data-cart-count]').forEach(node => node.textContent = payload.count);
  };
  const post = async (url, data) => {
    data.append(CSRF.name, CSRF.hash);
    const response = await fetch(url, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: data });
    const payload = await response.json();
    if (payload.csrfHash) CSRF.hash = payload.csrfHash;
    if (!response.ok) throw new Error(payload.message || 'Unable to update cart');
    return payload;
  };

  document.querySelector('.cart-trigger')?.addEventListener('click', openDrawer);
  document.querySelector('.drawer-close')?.addEventListener('click', closeDrawer);
  scrim?.addEventListener('click', closeDrawer);
  const menuButton = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('.site-header nav');
  const navigationOverlay = document.querySelector('.store-nav-overlay');
  const setMenuOpen = open => {
    navigation?.classList.toggle('open', open);
    navigationOverlay?.classList.toggle('open', open);
    menuButton?.classList.toggle('open', open);
    menuButton?.setAttribute('aria-expanded', open ? 'true' : 'false');
    menuButton?.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    document.body.classList.toggle('store-menu-open', open);
  };
  menuButton?.addEventListener('click', () => setMenuOpen(!navigation?.classList.contains('open')));
  navigationOverlay?.addEventListener('click', () => setMenuOpen(false));
  navigation?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => setMenuOpen(false)));
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') setMenuOpen(false);
  });

  document.addEventListener('click', event => {
    const thumbnail = event.target.closest('.card-thumbnail');
    if (!thumbnail) return;
    const card = thumbnail.closest('.product-card');
    const mainImage = card?.querySelector('.card-main-image');
    if (!mainImage) return;
    mainImage.src = thumbnail.dataset.cardImage;
    card.querySelectorAll('.card-thumbnail').forEach(item => {
      const active = item === thumbnail;
      item.classList.toggle('active', active);
      item.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  });

  document.querySelectorAll('.quantity-stepper').forEach(stepper => {
    stepper.addEventListener('click', async event => {
      const button = event.target.closest('button[data-delta]');
      if (!button || button.disabled || stepper.dataset.busy === 'true') return;
      stepper.dataset.busy = 'true';
      const data = new FormData();
      data.append('product_id', stepper.dataset.product);
      data.append('delta', button.dataset.delta);
      try {
        const payload = await post('/cart/change', data);
        syncResponse(payload);
        const output = stepper.querySelector('output');
        output.textContent = payload.productQuantity;
        stepper.querySelector('[data-delta="-1"]').disabled = payload.productQuantity < 1;
        stepper.querySelector('[data-delta="1"]').disabled = payload.productQuantity >= Number(stepper.dataset.stock);
        notify(payload.message);
      } catch (error) { notify(error.message); }
      finally { stepper.dataset.busy = 'false'; }
    });
  });

  document.querySelector('.add-form')?.addEventListener('submit', async event => {
    event.preventDefault();
    try { const payload = await post('/cart/add', new FormData(event.currentTarget)); syncResponse(payload); notify(payload.message); openDrawer(); }
    catch (error) { notify(error.message); }
  });
})();

/* Premium storefront motion */
(() => {
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduceMotion) return;

  const revealTargets = document.querySelectorAll(
    'main > section:not(.orders-page):not(.order-detail-page), .product-card, .why-pick1-grid article, .customer-review'
  );
  revealTargets.forEach((element, index) => {
    element.classList.add('reveal-section');
    element.style.setProperty('--reveal-delay', `${Math.min(index % 6, 5) * 55}ms`);
  });

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-revealed');
      observer.unobserve(entry.target);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -45px' });
  revealTargets.forEach(element => observer.observe(element));

  document.querySelectorAll(
    '.button, .numae-shop, .checkout-pay-button, .product-go-cart, .quantity-stepper button, .numae-footer button'
  ).forEach(control => {
    control.classList.add('has-ripple');
    control.addEventListener('pointerdown', event => {
      const bounds = control.getBoundingClientRect();
      const ripple = document.createElement('span');
      ripple.className = 'button-ripple';
      ripple.style.left = `${event.clientX - bounds.left}px`;
      ripple.style.top = `${event.clientY - bounds.top}px`;
      control.append(ripple);
      ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
    });
  });

  if (document.querySelector('.home-carousel, .numae-hero')) {
    const leaves = document.createElement('div');
    leaves.className = 'floating-leaves';
    leaves.setAttribute('aria-hidden', 'true');
    for (let index = 0; index < 7; index++) {
      const leaf = document.createElement('i');
      leaf.style.setProperty('--leaf-index', index);
      leaves.append(leaf);
    }
    document.body.append(leaves);
  }
})();
