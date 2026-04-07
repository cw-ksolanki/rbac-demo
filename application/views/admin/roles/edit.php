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

            <div class="mb-3">
                <label class="form-label fw-semibold">Role Name</label>
                <input type="text" name='name' class="form-control" value="<?= htmlspecialchars($role->name) ?>">
                <div class="form-text">Built-in role name cannot be changed.</div>
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