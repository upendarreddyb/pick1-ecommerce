<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?><script src="https://checkout.razorpay.com/v1/checkout.js"></script><?= $this->endSection() ?>
<?= $this->section('content') ?>
<section class="payment-review">
  <div class="payment-review-card">
    <p class="checkout-kicker">Order <?= esc(order_number($order)) ?></p>
    <h1>Review and pay</h1>
    <div class="payment-mini-items"><?php foreach($items as $item): ?><div><span><?= esc($item['name']) ?> × <?= $item['quantity'] ?></span><strong>₹<?= number_format(($item['sale_price']?:$item['price'])*$item['quantity'],2) ?></strong></div><?php endforeach ?></div>
    <div class="payment-due"><span>Total payable</span><strong>₹<?= number_format($order['total_amount'],2) ?></strong></div>
    <?php if($paymentMethod==='gpay'): ?>
      <button id="pay" type="button" class="gateway-button gpay-button"><span class="gpay-g">G</span> Pay with Google Pay</button>
      <p>Google Pay opens through Razorpay’s secure UPI checkout. Availability depends on your device and enabled Razorpay payment methods.</p>
    <?php else: ?>
      <button id="pay" type="button" class="gateway-button razorpay-button"><span>R</span> Pay securely with Razorpay</button>
      <p>Choose card, UPI, netbanking, wallet, or another enabled method inside Razorpay.</p>
    <?php endif ?>
    <p id="payment-message" role="status" aria-live="polite"></p>
    <a class="change-payment" href="<?= base_url('checkout') ?>">← Change payment method</a>
  </div>
</section>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
(() => {
  const payButton = document.querySelector('#pay');
  const message = document.querySelector('#payment-message');
  const internalOrderId = <?= json_encode((int) $order['id']) ?>;
  const publicOrderId = <?= json_encode(order_number($order)) ?>;
  let csrfName = <?= json_encode(csrf_token()) ?>;
  let csrfHash = <?= json_encode(csrf_hash()) ?>;

  const showMessage = (text, isError = false) => {
    message.textContent = text;
    message.style.color = isError ? '#a32020' : '';
  };

  const post = async (url, fields) => {
    const body = new URLSearchParams({ ...fields, [csrfName]: csrfHash });
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8', 'Accept': 'application/json' },
      body,
      credentials: 'same-origin'
    });
    const data = await response.json().catch(() => ({ message: 'The server returned an invalid response.' }));
    if (data.csrf_name && data.csrf_hash) {
      csrfName = data.csrf_name;
      csrfHash = data.csrf_hash;
    }
    if (!response.ok || data.success === false) {
      throw new Error(data.message || 'Payment request failed.');
    }
    return data;
  };

  payButton.addEventListener('click', async () => {
    payButton.disabled = true;
    showMessage('Preparing secure payment…');

    try {
      if (typeof Razorpay === 'undefined') {
        throw new Error('Razorpay Checkout could not load. Check your connection and try again.');
      }

      const gateway = await post(<?= json_encode(base_url('api/create-order')) ?>, {
        order_id: internalOrderId
      });

      const options = {
        key: <?= json_encode((string) $key) ?>,
        amount: gateway.amount,
        currency: gateway.currency,
        name: 'Pick1',
        description: 'Order ' + publicOrderId,
        order_id: gateway.order_id,
        prefill: <?= json_encode($prefill, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>,
        handler: async response => {
          showMessage('Verifying payment…');
          try {
            const result = await post(<?= json_encode(base_url('api/verify-payment')) ?>, {
              order_id: internalOrderId,
              razorpay_order_id: response.razorpay_order_id,
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_signature: response.razorpay_signature
            });
            window.location.assign(result.redirect);
          } catch (error) {
            payButton.disabled = false;
            showMessage(error.message, true);
          }
        },
        modal: {
          ondismiss: () => {
            payButton.disabled = false;
            showMessage('Payment cancelled. You can try again.', true);
          }
        },
        theme: { color: '#155d45' },
        retry: { enabled: true }
      };

      <?php if($paymentMethod==='gpay'): ?>
      options.config = { display: { blocks: { gpay: { name: 'Pay with Google Pay', instruments: [{ method: 'upi', flows: ['intent'], apps: ['google_pay'] }] } }, sequence: ['block.gpay', 'upi'], preferences: { show_default_blocks: true } } };
      <?php endif ?>

      const checkout = new Razorpay(options);
      checkout.on('payment.failed', response => {
        payButton.disabled = false;
        showMessage(response.error?.description || 'Payment failed. Please try again.', true);
      });
      checkout.open();
      showMessage('');
    } catch (error) {
      payButton.disabled = false;
      showMessage(error.message, true);
    }
  });
})();
</script>
<?= $this->endSection() ?>
