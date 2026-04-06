<?php $this->load->view('admin/layouts/header', ['page_title' => 'Edit Role']); ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Edit Role — <code><?= htmlspecialchars($role->name) ?></code></div>
    <div class="card-body" style="max-width: 600px;">
        <?php echo form_open('admin/roles/edit/' . $role->id); ?>

            <?php if (!in_array($role->name, $protected)): ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                    value="<?= set_value('name', $role->name) ?>" required>
                <div class="form-text text-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Changing the role name will NOT rename the existing profile table.
                </div>
            </div>
            <?php else: ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Name</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($role->name) ?>" disabled>
                <div class="form-text">Built-in role name cannot be changed.</div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                <input type="text" name="display_name" class="form-control"
                    value="<?= set_value('display_name', $role->display_name) ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Description</label>
                <input type="text" name="description" class="form-control"
                    value="<?= set_value('description', $role->description) ?>">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>

        <?php echo form_close(); ?>
    </div>
</div>

<?php $this->load->view('admin/layouts/footer'); ?>