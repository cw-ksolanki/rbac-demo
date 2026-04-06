<?php $this->load->view('admin/layouts/header', ['page_title' => 'Create Role']); ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Create New Role</div>
    <div class="card-body" style="max-width: 700px;">
        <?php echo form_open('admin/roles/create'); ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                    placeholder="e.g. manager (lowercase, no spaces)"
                    value="<?= set_value('name') ?>" required>
                <div class="form-text">Used as identifier and table name prefix. Lowercase letters and underscores only.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                <input type="text" name="display_name" class="form-control"
                    placeholder="e.g. Manager"
                    value="<?= set_value('display_name') ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Description</label>
                <input type="text" name="description" class="form-control"
                    placeholder="Optional description"
                    value="<?= set_value('description') ?>">
            </div>

            <hr>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <div class="fw-semibold">Custom Profile Fields</div>
                        <div class="text-muted" style="font-size:13px;">
                            These fields will be added to <code id="tablePreview">role_name_profiles</code> table.
                            Default fields (id, user_id, last_login, profile_pic, updated_at, updated_by) are always included.
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addField()">
                        <i class="bi bi-plus-lg"></i> Add Field
                    </button>
                </div>

                <div id="fields-container">
                    <!-- dynamic rows here -->
                </div>

                <div id="no-fields" class="text-muted text-center py-3" style="font-size:13px;border:1px dashed #dee2e6;border-radius:8px;">
                    No custom fields added. Click "Add Field" to add.
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
// Update table name preview as admin types role name
document.querySelector('input[name="name"]').addEventListener('input', function() {
    const val = this.value.trim().toLowerCase().replace(/[^a-z_]/g, '_') || 'role_name';
    document.getElementById('tablePreview').textContent = val + '_profiles';
});

const fieldTypes = <?= json_encode($field_types) ?>;
let fieldCount = 0;

function addField() {
    fieldCount++;
    document.getElementById('no-fields').style.display = 'none';

    // Build type options
    let options = '';
    for (const [val, label] of Object.entries(fieldTypes)) {
        options += `<option value="${val}">${label}</option>`;
    }

    const row = document.createElement('div');
    row.className = 'd-flex gap-2 align-items-center mb-2';
    row.id = 'field_row_' + fieldCount;
    row.innerHTML = `
        <input type="text" name="field_name[]" class="form-control form-control-sm"
            placeholder="Field name (e.g. address)" style="flex:1;">
        <select name="field_type[]" class="form-select form-select-sm" style="flex:1;">
            ${options}
        </select>
        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeField(${fieldCount})">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    document.getElementById('fields-container').appendChild(row);
}

function removeField(id) {
    const row = document.getElementById('field_row_' + id);
    if (row) row.remove();
    if (document.getElementById('fields-container').children.length === 0) {
        document.getElementById('no-fields').style.display = 'block';
    }
}
</script>

<?php $this->load->view('admin/layouts/footer'); ?>