<?php $this->load->view('admin/layouts/header', ['page_title' => 'Create Role']); ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Create New Role</div>
    <div class="card-body" style="max-width: 700px;">
        <?php echo form_open('admin/roles/create'); ?>

        <!-- Role Name -->
        <div class="mb-3">
            <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control"
                placeholder="e.g. manager"
                value="<?= set_value('name') ?>" required>
            <div class="form-text">Lowercase and underscores only.</div>
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Description</label>
            <input type="text" name="description" class="form-control"
                value="<?= set_value('description') ?>">
        </div>

        <hr>

        <!-- Checkbox Selection -->
        <div class="mb-3">
            <div class="fw-semibold mb-2">Select Profile Fields</div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="driver_fields" value="1"
                    id="driver_fields" <?= set_value('driver_fields') ? 'checked' : '' ?>>
                <label class="form-check-label">Driver Fields</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="user_fields" value="1"
                    id="user_fields" <?= set_value('user_fields') ? 'checked' : '' ?>>
                <label class="form-check-label">User Fields</label>
            </div>
        </div>

        <!-- Preview Fields -->
        <div id="fields_preview" class="mt-3 row g-3"></div>

        <hr>

        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Create Role</button>
            <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-secondary">Cancel</a>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const driverCheckbox = document.getElementById('driver_fields');
    const userCheckbox   = document.getElementById('user_fields');
    const preview        = document.getElementById('fields_preview');

    driverCheckbox.addEventListener('change', renderFields);
    userCheckbox.addEventListener('change', renderFields);

    // Run on load (important for validation case)
    renderFields();

    function renderFields() {
        let fields = [];

        if (driverCheckbox.checked) {
            fields.push('vehicle_no', 'vehicle_type', 'licence_no');
        }

        if (userCheckbox.checked) {
            fields.push('company', 'company_website');
        }

        fields = [...new Set(fields)];

        if (fields.length === 0) {
            preview.innerHTML = `<div class="text-muted">No fields selected</div>`;
            return;
        }

        let html = '';
        fields.forEach(name => {
            html += buildFieldPreview(name);
        });

        preview.innerHTML = html;
    }

    function buildFieldPreview(name) {
        const label = name
            .replace(/_/g, ' ')
            .replace(/\b\w/g, c => c.toUpperCase());

        return `<div class="col-md-6">
            <label class="form-label fw-semibold">${label}</label>
            <input type="text" class="form-control" disabled placeholder="${label}">
        </div>`;
    }
});
</script>

<?php $this->load->view('admin/layouts/footer'); ?>