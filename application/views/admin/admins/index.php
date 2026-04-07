<?php $this->load->view('admin/layouts/header', ['page_title' => 'Admins']); ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>
<!-- Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Admins <span class="text-muted fw-normal" style="font-size:13px;">(<?= $total ?>)</span></span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="ps-4"><?= $u->id ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;background:#eef2ff;color:#4f46e5;
                                        border-radius:50%;display:flex;align-items:center;
                                        justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">
                                <?= strtoupper(substr($u->name, 0, 1)) ?>
                            </div>
                            <?= htmlspecialchars($u->name) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($u->email) ?></td>
                    <td><?= htmlspecialchars($u->phone ?? '—') ?></td>
                    <td>
                        <span class="badge" style="background:#f0f0ff;color:#4f46e5;">
                            <?= htmlspecialchars($u->role_name) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($u->status === 'active'): ?>
                            <span class="badge bg-success-subtle text-success">Active</span>
                        <?php elseif ($u->status === 'inactive'): ?>
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger">Banned</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d M Y', strtotime($u->created_at)) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No admins found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('admin/layouts/footer'); ?>