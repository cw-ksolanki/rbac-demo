<?php $this->load->view('admin/layouts/header', ['page_title' => 'Roles']); ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>All Roles</span>
        <a href="<?= site_url('admin/roles/create') ?>" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> New Role
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Name</th>
                    <th>Display Name</th>
                    <th>Description</th>
                    <th>Profile Table</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($roles)): ?>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td class="ps-4"><?= $role->id ?></td>
                    <td><code><?= htmlspecialchars($role->name) ?></code></td>
                    <td><?= htmlspecialchars($role->display_name) ?></td>
                    <td class="text-muted"><?= htmlspecialchars($role->description ?? '—') ?></td>
                    <td><code><?= $role->name ?>_profiles</code></td>
                    <td>
                        <a href="<?= site_url('admin/roles/edit/' . $role->id) ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <?php if (!in_array($role->name, ['admin', 'driver', 'user'])): ?>
                        <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmDelete(<?= $role->id ?>, '<?= htmlspecialchars($role->display_name) ?>')">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                        <?php else: ?>
                        <span class="text-muted" style="font-size:12px;">built-in</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted py-4">No roles found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Delete Role</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-0">Are you sure you want to delete <strong id="roleName"></strong>?
                This will also <span class="text-danger">drop the profile table</span> for this role.</p>
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
    document.getElementById('roleName').textContent = name;
    document.getElementById('deleteBtn').href = '<?= site_url('admin/roles/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php $this->load->view('admin/layouts/footer'); ?>