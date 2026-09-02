<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<section class="stats">
  <article class="stat"><small>Total orders</small><strong><?= number_format($stats['orders']) ?></strong><p>All customer orders</p></article>
  <article class="stat"><small>Paid revenue</small><strong>₹<?= number_format($stats['revenue']) ?></strong><p>Verified payments only</p></article>
  <article class="stat"><small>Pending orders</small><strong><?= number_format($stats['pending']) ?></strong><p>Awaiting action</p></article>
  <article class="stat"><small>Active catalog</small><strong><?= number_format($stats['products']) ?></strong><p>Products in your store</p></article>
  <a class="stat stat-notifications" href="<?= base_url('admin/order-notifications') ?>" data-notification-card data-count-url="<?= base_url('admin/order-notifications/count') ?>">
    <?php if ($unreadOrderCount > 0): ?><span class="stat-alert-badge" data-notification-badge><?= $unreadOrderCount > 99 ? '99+' : $unreadOrderCount ?></span><?php endif ?>
    <small>Notifications</small><strong data-notification-count aria-live="polite"><?= number_format($unreadOrderCount) ?></strong><p data-notification-message><?= $unreadOrderCount > 0 ? 'New orders — click to view' : 'No new order notifications' ?></p>
  </a>
</section>
<section class="dashboard-grid">
  <div class="panel"><div class="panel-head"><h2>Store overview</h2><a href="<?= base_url('/') ?>" target="_blank">Open storefront →</a></div><div class="store-health"><div class="health-item"><span></span><strong>Store online</strong><small>Customer pages available</small></div><div class="health-item"><span></span><strong>Catalog connected</strong><small>Admin changes are live</small></div><div class="health-item"><span></span><strong>Secure checkout</strong><small>Server-side verification</small></div></div></div>
  <div class="panel"><div class="panel-head"><h2>Quick actions</h2></div><div class="quick-actions"><a href="<?= base_url('admin/products/new') ?>"><span>Add a product</span><b>+</b></a><a href="<?= base_url('admin/orders') ?>"><span>Review orders</span><b>→</b></a><a href="<?= base_url('admin/payments') ?>"><span>View payments</span><b>→</b></a></div></div>
</section>
<script>
(()=>{
  const card=document.querySelector('[data-notification-card]');
  if(!card)return;
  const countElement=card.querySelector('[data-notification-count]');
  const message=card.querySelector('[data-notification-message]');
  const update=async()=>{
    if(document.hidden)return;
    try{
      const response=await fetch(card.dataset.countUrl,{headers:{Accept:'application/json'},cache:'no-store',credentials:'same-origin'});
      if(!response.ok)return;
      const count=Math.max(0,Number((await response.json()).count)||0);
      countElement.textContent=count.toLocaleString('en-IN');
      message.textContent=count>0?'New orders — click to view':'No new order notifications';
      let badge=card.querySelector('[data-notification-badge]');
      if(count>0){
        if(!badge){badge=document.createElement('span');badge.className='stat-alert-badge';badge.dataset.notificationBadge='';card.appendChild(badge)}
        badge.textContent=count>99?'99+':String(count);
      }else badge?.remove();
    }catch(error){}
  };
  update();
  window.setInterval(update,10000);
  document.addEventListener('visibilitychange',()=>{if(!document.hidden)update()});
})();
</script>
<?= $this->endSection() ?>
