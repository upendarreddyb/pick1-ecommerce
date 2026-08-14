<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Name</th><th>Email</th><th>Message</th><th>Status</th></tr></thead><tbody>
<?php if (! $rows): ?><tr><td colspan="5">No contact messages yet.</td></tr><?php endif ?>
<?php foreach ($rows as $row): ?><tr>
  <td><?= esc(date('d M Y, h:i A', strtotime((string) $row['created_at']))) ?></td>
  <td><?= esc($row['name']) ?></td>
  <td><a href="mailto:<?= esc($row['email']) ?>"><?= esc($row['email']) ?></a></td>
  <td><?= nl2br(esc($row['message'])) ?></td>
  <td><form method="post" action="<?= base_url('admin/contact-messages/' . $row['id'] . '/status') ?>"><?= csrf_field() ?><select name="status" onchange="this.form.submit()"><?php foreach (['new','read','resolved'] as $status): ?><option value="<?= $status ?>" <?= $row['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option><?php endforeach ?></select></form></td>
</tr><?php endforeach ?>
</tbody></table></div>
<?= $this->endSection() ?>
