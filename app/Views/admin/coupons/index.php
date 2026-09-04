<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<form class="form-card coupon-create-form" method="post" action="<?= base_url('admin/coupons') ?>">
  <?= csrf_field() ?>
  <label>Coupon code<input name="code" maxlength="50" value="<?= esc(old('code')) ?>" placeholder="Example: PICK10" required></label>
  <div><small>Every active coupon gives customers 10% off the product subtotal.</small><button class="btn" type="submit">Add coupon</button></div>
</form>
<div class="table-wrap" style="margin-top:25px"><table><thead><tr><th>Coupon</th><th>Discount</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><strong><?= esc($row['code']) ?></strong></td><td>10%</td><td><span class="status"><?= esc(ucfirst($row['status'])) ?></span></td><td><?= date('d M Y', strtotime($row['created_at'])) ?></td><td class="coupon-actions"><form method="post" action="<?= base_url('admin/coupons/' . $row['id'] . '/status') ?>"><?= csrf_field() ?><input type="hidden" name="status" value="<?= $row['status'] === 'active' ? 'inactive' : 'active' ?>"><button type="submit"><?= $row['status'] === 'active' ? 'Disable' : 'Enable' ?></button></form><form method="post" action="<?= base_url('admin/coupons/' . $row['id'] . '/delete') ?>" onsubmit="return confirm('Delete this coupon?')"><?= csrf_field() ?><button type="submit">Delete</button></form></td></tr><?php endforeach ?>
<?php if (! $rows): ?><tr><td colspan="5">No coupons added yet.</td></tr><?php endif ?>
</tbody></table></div>
<?= $this->endSection() ?>
