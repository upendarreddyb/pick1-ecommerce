<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<details class="dashboard-notifications" data-read-url="<?= base_url('admin/order-notifications/read') ?>" data-unread-count="<?= (int) $unreadOrderCount ?>">
  <summary>
    <span class="dashboard-notification-bell"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg><?php if ($unreadOrderCount > 0): ?><b><?= $unreadOrderCount > 99 ? '99+' : $unreadOrderCount ?></b><?php endif ?></span>
    <span><strong>Order notifications</strong><small><?= $unreadOrderCount > 0 ? number_format($unreadOrderCount) . ' new paid order' . ($unreadOrderCount === 1 ? '' : 's') : 'You are all caught up' ?></small></span>
    <span class="dashboard-notification-action"><?= $unreadOrderCount > 0 ? 'View new orders' : 'No new orders' ?> <b>⌄</b></span>
  </summary>
  <div class="dashboard-notification-content">
    <?php if ($recentOrders): ?>
      <div class="admin-notification-list">
        <?php foreach ($recentOrders as $newOrder): ?>
          <a href="<?= base_url('admin/orders/' . $newOrder['id']) ?>">
            <span class="admin-notification-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"></path><path d="M9 8h6M9 12h6"></path></svg></span>
            <span class="admin-notification-copy"><strong><?= esc(order_number($newOrder)) ?></strong><small><?= esc($newOrder['full_name'] ?: 'Customer') ?> · ₹<?= number_format((float) $newOrder['total_amount'], 2) ?></small><time><?= esc(date('d M Y, h:i A', strtotime((string) $newOrder['created_at']))) ?></time></span>
            <span class="status"><?= esc(ucfirst((string) $newOrder['status'])) ?></span>
          </a>
        <?php endforeach ?>
      </div>
      <a class="admin-notification-all" href="<?= base_url('admin/orders') ?>">View all orders <span>→</span></a>
    <?php else: ?>
      <div class="admin-notification-empty"><span>✓</span><strong>No new orders</strong><small>New paid orders will appear here.</small></div>
    <?php endif ?>
  </div>
</details>
<section class="stats">
  <article class="stat"><small>Total orders</small><strong><?= number_format($stats['orders']) ?></strong><p>All customer orders</p></article>
  <article class="stat"><small>Paid revenue</small><strong>₹<?= number_format($stats['revenue']) ?></strong><p>Verified payments only</p></article>
  <article class="stat"><small>Pending orders</small><strong><?= number_format($stats['pending']) ?></strong><p>Awaiting action</p></article>
  <article class="stat"><small>Active catalog</small><strong><?= number_format($stats['products']) ?></strong><p>Products in your store</p></article>
</section>
<section class="dashboard-grid">
  <div class="panel"><div class="panel-head"><h2>Store overview</h2><a href="<?= base_url('/') ?>" target="_blank">Open storefront →</a></div><div class="store-health"><div class="health-item"><span></span><strong>Store online</strong><small>Customer pages available</small></div><div class="health-item"><span></span><strong>Catalog connected</strong><small>Admin changes are live</small></div><div class="health-item"><span></span><strong>Secure checkout</strong><small>Server-side verification</small></div></div></div>
  <div class="panel"><div class="panel-head"><h2>Quick actions</h2></div><div class="quick-actions"><a href="<?= base_url('admin/products/new') ?>"><span>Add a product</span><b>+</b></a><a href="<?= base_url('admin/orders') ?>"><span>Review orders</span><b>→</b></a><a href="<?= base_url('admin/payments') ?>"><span>View payments</span><b>→</b></a></div></div>
</section>
<script>
(()=>{
  const notifications=document.querySelector('.dashboard-notifications');
  if(!notifications||Number(notifications.dataset.unreadCount)===0)return;
  notifications.addEventListener('toggle',async()=>{
    if(!notifications.open||notifications.dataset.read==='true')return;
    notifications.dataset.read='true';
    try{
      const response=await fetch(notifications.dataset.readUrl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},body:new URLSearchParams({'<?= csrf_token() ?>':'<?= csrf_hash() ?>'})});
      if(!response.ok)throw new Error('Unable to mark notifications as read');
      notifications.querySelector('.dashboard-notification-bell b')?.remove();
      notifications.querySelector('summary small').textContent='You are all caught up';
      const action=notifications.querySelector('.dashboard-notification-action');
      if(action)action.childNodes[0].textContent='Viewed ';
    }catch(error){notifications.dataset.read='false'}
  });
})();
</script>
<?= $this->endSection() ?>
