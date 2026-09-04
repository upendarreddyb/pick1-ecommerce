<?= $this->extend('layouts/store') ?>
<?= $this->section('head') ?><link rel="stylesheet" href="<?= base_url('assets/css/policies.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/policies.css') ?>"><?= $this->endSection() ?>
<?= $this->section('content') ?>
<article class="policy-page">
  <header class="policy-header"><p class="policy-kicker">Pick1 policies</p><h1>Shipping Policy</h1><p class="policy-effective">Effective date: August 1, 2026</p></header>
  <div class="policy-content">
    <p>At Pick1, we are committed to delivering your orders safely, securely, and on time. Please read our Shipping Policy to understand how your orders are processed, shipped, and delivered.</p>

    <section><h2>Order Processing</h2>
      <ul><li>All orders are processed within 1–2 business days after successful confirmation.</li><li>Orders are processed Monday through Saturday, excluding Sundays and public holidays.</li><li>After dispatch, you will receive a shipping confirmation email and/or SMS containing tracking details.</li><li>Orders placed on Sundays or public holidays will be processed on the next business day.</li></ul>
    </section>

    <section><h2>Shipping Coverage</h2><p>We currently deliver orders across India.</p><p>If your location is not serviceable by our courier partners, our customer support team will contact you to discuss available alternatives or process a refund, if applicable.</p></section>

    <section><h2>Shipping Charges</h2>
      <ul><li><strong>Standard shipping:</strong> ₹49 for orders below ₹350.</li><li><strong>Free shipping:</strong> Free standard shipping is available on orders of ₹350 or more.</li><li>Shipping offers may be modified or withdrawn without prior notice.</li></ul>
    </section>

    <section><h2>Estimated Delivery Time</h2><p>Delivery timelines are estimates and may vary because of weather conditions, natural disasters, public holidays, courier delays, government restrictions, or accessibility of remote locations.</p></section>

    <section><h2>Order Tracking</h2><p>After your order ships, you will receive the tracking number, courier partner name, and a tracking link where applicable. Please allow up to 24 hours after dispatch for tracking information to become active.</p></section>

    <section><h2>Delivery Attempts</h2><p>Our courier partners generally make two to three delivery attempts. A shipment may be returned if delivery cannot be completed because of an incorrect address or contact number, customer unavailability, or refusal to accept delivery. Additional shipping charges may apply if you request re-shipping.</p></section>

    <section><h2>Incorrect Shipping Address</h2><p>Customers are responsible for providing a complete and accurate shipping address and valid contact number. Pick1 is not responsible for delays, failed deliveries, or returned shipments caused by incorrect or incomplete information supplied by the customer.</p></section>

    <section><h2>Damaged or Tampered Packages</h2>
      <p>If your package appears damaged or tampered with:</p>
      <ul><li>Photograph the outer packaging before opening the parcel.</li><li>If possible, refuse delivery when the package is significantly damaged.</li><li>Contact our support team within 24 hours of receipt.</li><li>Include your order number and photographs.</li></ul>
      <p>Our team will review your request under our <a href="<?= base_url('return-refund-policy') ?>">Return &amp; Refund Policy</a>.</p>
    </section>

    <section><h2>Missing or Delayed Orders</h2><p>If your order has not arrived within 10 business days after shipping confirmation, contact us with your full name, order number, registered mobile number, and email address. Our team will coordinate with the courier and provide an update.</p></section>

    <section><h2>Shipping Delays</h2><p>Pick1 is not liable for shipping delays caused by courier issues, weather, natural disasters, government regulations, public holidays, transportation disruptions, or force majeure events. We appreciate your patience in such situations.</p></section>

    <section><h2>International Shipping</h2><p>Pick1 currently ships only within India. International shipping is not available.</p></section>

    <aside class="policy-contact"><h2>Contact Us</h2><p>Pick1 (Shatavari Ayurvedic and Pharmaceutical Private Limited)</p><p>Email: <a href="mailto:info@pick1.in">info@pick1.in</a><br>Website: <a href="https://pick1.in">pick1.in</a></p></aside>
  </div>
</article>
<?= $this->endSection() ?>
