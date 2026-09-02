<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<section class="stats">
  <article class="stat"><small>Total orders</small><strong><?= number_format($stats['orders']) ?></strong><p>All customer orders</p></article>
  <article class="stat"><small>Paid revenue</small><strong>₹<?= number_format($stats['revenue']) ?></strong><p>Verified payments only</p></article>
  <article class="stat"><small>Pending orders</small><strong><?= number_format($stats['pending']) ?></strong><p>Awaiting action</p></article>
  <article class="stat"><small>Active catalog</small><strong><?= number_format($stats['products']) ?></strong><p>Products in your store</p></article>
</section>
<section class="order-inbox">
  <details class="dashboard-notifications" data-read-url="<?= base_url('admin/order-notifications/read') ?>" data-unread-count="<?= (int) $unreadOrderCount ?>">
    <summary>
      <span class="dashboard-notification-bell"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path><path d="M10 21h4"></path></svg></span>
      <span class="order-inbox-heading"><strong>Order inbox</strong><small data-notification-caption><?= $unreadOrderCount > 0 ? 'New customer orders need your attention' : 'No unread order notifications' ?></small></span>
      <?php if ($unreadOrderCount > 0): ?><span class="order-unread-badge" data-notification-badge><b><?= $unreadOrderCount > 99 ? '99+' : $unreadOrderCount ?></b> unread</span><?php endif ?>
      <span class="dashboard-notification-action"><span><?= $unreadOrderCount > 0 ? 'View orders' : 'Check orders' ?></span><b>⌄</b></span>
    </summary>
    <div class="dashboard-notification-content">
      <?php if ($recentOrders): ?>
        <div class="order-notification-labels"><span>Order</span><span>Amount</span><span>Status</span><span></span></div>
        <div class="admin-notification-list">
          <?php foreach ($recentOrders as $newOrder): ?>
            <a href="<?= base_url('admin/orders/' . $newOrder['id']) ?>">
              <span class="admin-notification-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Z"></path><path d="M9 8h6M9 12h6"></path></svg></span>
              <span class="admin-notification-copy"><strong><?= esc(order_number($newOrder)) ?></strong><small><?= esc($newOrder['full_name'] ?: 'Customer') ?></small><time><?= esc(date('d M Y, h:i A', strtotime((string) $newOrder['created_at']))) ?></time></span>
              <strong class="order-notification-amount">₹<?= number_format((float) $newOrder['total_amount'], 2) ?></strong>
              <span class="status"><?= esc(ucfirst((string) $newOrder['status'])) ?></span>
              <span class="order-notification-open">View <b>→</b></span>
            </a>
          <?php endforeach ?>
        </div>
        <footer><span>Showing up to 10 unread orders</span><a href="<?= base_url('admin/orders') ?>">View all orders <b>→</b></a></footer>
      <?php else: ?>
        <div class="admin-notification-empty"><span>✓</span><strong>You’re all caught up</strong><small>New paid orders will appear here automatically.</small></div>
      <?php endif ?>
    </div>
  </details>
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
      notifications.querySelector('[data-notification-badge]')?.remove();
      const caption=notifications.querySelector('[data-notification-caption]');
      if(caption)caption.textContent='Notifications marked as viewed';
    }catch(error){notifications.dataset.read='false'}
  });
})();
</script>
<?= $this->endSection() ?>
