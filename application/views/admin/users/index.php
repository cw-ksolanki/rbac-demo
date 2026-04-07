<?php $this->load->view('admin/layouts/header', ['page_title' => 'Users']); ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body py-3">
        <?php echo form_open('', ['method' => 'get', 'class' => 'd-flex gap-2 flex-wrap align-items-end']); ?>
            <div>
                <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Search</label>
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Name, email, phone..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                    style="width:220px;">
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Role</label>
                <select name="role_id" class="form-select form-select-sm" style="width:150px;">
                    <option value="">All Roles</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->id ?>"
                            <?= ($filters['role_id'] == $role->id) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role->display_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label mb-1" style="font-size:12px;font-weight:600;">Status</label>
                <select name="status" class="form-select form-select-sm" style="width:130px;">
                    <option value="">All Status</option>
                    <option value="active"   <?= ($filters['status'] === 'active')   ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($filters['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    <option value="banned"   <?= ($filters['status'] === 'banned')   ? 'selected' : '' ?>>Banned</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Users <span class="text-muted fw-normal" style="font-size:13px;">(<?= $total ?>)</span></span>
        <a href="<?= site_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New User
        </a>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                <?php $is_admin = ($u->role_name === 'admin'); ?>
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
                            <?= htmlspecialchars($u->role_display_name ?? $u->role_name) ?>
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
                    <td>
                        <a href="<?= site_url('admin/users/view/' . $u->id) ?>"
                           class="btn btn-sm btn-outline-secondary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if (!$is_admin): ?>
                        <a href="<?= site_url('admin/users/edit/' . $u->id) ?>"
                           class="btn btn-sm btn-outline-secondary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-danger" title="Delete"
                            onclick="confirmDelete(<?= $u->id ?>, '<?= htmlspecialchars($u->name) ?>')">
                            <i class="bi bi-trash"></i>
                        </button>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:11px;">admin</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No users found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total > $per_page): ?>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <span style="font-size:13px;color:#6c757d;">
            Showing <?= min(($current_page - 1) * $per_page + 1, $total) ?>–<?= min($current_page * $per_page, $total) ?>
            of <?= $total ?> users
        </span>
        <?= $pagination ?>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Delete User</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-0">Are you sure you want to delete <strong id="userName"></strong>?
                This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteBtn" href="#" class="btn btn-sm btn-danger">Yes, Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('userName').textContent = name;
    document.getElementById('deleteBtn').href = '<?= site_url('admin/users/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php $this->load->view('admin/layouts/footer'); ?>