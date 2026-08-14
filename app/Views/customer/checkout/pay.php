<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?><script src="https://checkout.razorpay.com/v1/checkout.js"></script><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="payment-review">
  <div class="payment-review-card">
    <p class="checkout-kicker">Order #<?= $order['id'] ?></p>
    <h1>Review and pay</h1>
    <div class="payment-mini-items"><?php foreach($items as $item): ?><div><span><?= esc($item['name']) ?> × <?= $item['quantity'] ?></span><strong>₹<?= number_format(($item['sale_price']?:$item['price'])*$item['quantity'],2) ?></strong></div><?php endforeach ?></div>
    <div class="payment-due"><span>Total payable</span><strong>₹<?= number_format($order['total_amount'],2) ?></strong></div>
    <?php if($paymentMethod==='gpay'): ?>
      <button id="pay" class="gateway-button gpay-button"><span class="gpay-g">G</span> Pay with Google Pay</button>
      <p>Google Pay opens through Razorpay’s secure UPI checkout. Availability depends on your device and enabled Razorpay payment methods.</p>
    <?php else: ?>
      <button id="pay" class="gateway-button razorpay-button"><span>R</span> Pay securely with Razorpay</button>
      <p>Choose card, UPI, netbanking, wallet, or another enabled method inside Razorpay.</p>
    <?php endif ?>
    <a class="change-payment" href="<?= base_url('checkout') ?>">← Change payment method</a>
  </div>
  <form id="verify" method="post" action="<?= base_url('checkout/payment/verify') ?>"><?= csrf_field() ?><input type="hidden" name="order_id" value="<?= $order['id'] ?>"><input type="hidden" name="razorpay_order_id"><input type="hidden" name="razorpay_payment_id"><input type="hidden" name="razorpay_signature"></form>
</section>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
document.querySelector('#pay').addEventListener('click', () => {
  const options = {
    key: '<?= esc($key) ?>', amount: <?= (int)$gateway['amount'] ?>, currency: 'INR',
    name: 'Pick1', description: 'Order #<?= $order['id'] ?>', order_id: '<?= esc($gateway['id']) ?>',
    handler: response => { Object.keys(response).forEach(key => { const input=document.querySelector(`[name="${key}"]`); if(input) input.value=response[key]; }); document.querySelector('#verify').submit(); },
    theme: { color: '#111111' }, retry: { enabled: true }
  };
  <?php if($paymentMethod==='gpay'): ?>
  options.config = { display: { blocks: { gpay: { name: 'Pay with Google Pay', instruments: [{ method: 'upi', flows: ['intent'], apps: ['google_pay'] }] } }, sequence: ['block.gpay', 'upi'], preferences: { show_default_blocks: true } } };
  <?php endif ?>
  new Razorpay(options).open();
});
</script>
<?= $this->endSection() ?>
