<?= $this->extend('layouts/store') ?>
<?= $this->section('content') ?>
<section class="collection-page">
  <h1>Products</h1>
  <div class="collection-toolbar"><div><span>Filter:</span><details><summary>Availability</summary><a href="<?= base_url('products') ?>">All products</a></details><details><summary>Category</summary><?php foreach($categories as $c): ?><a href="?category=<?= $c['id'] ?>"><?= esc($c['name']) ?></a><?php endforeach ?></details></div><div><label for="sort">Sort by:</label><select id="sort"><option>Alphabetically, A–Z</option></select><span><?= count($products) ?> products</span></div></div>
  <div class="product-grid collection-grid"><?php foreach($products as $p): ?><?= view('customer/products/_card',['p'=>$p,'cartQuantity'=>$cartQuantities[(int)$p['id']]??0]) ?><?php endforeach ?></div>
<?php
// echo $pager->links();
?>
</section>
<?= $this->endSection() ?>
