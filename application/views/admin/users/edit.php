<?php $this->load->view('admin/layouts/header', ['page_title' => 'Edit User']); ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Edit User — <strong><?= htmlspecialchars($user->name) ?></strong></div>
    <div class="card-body" style="max-width:700px;">
        <?php echo form_open('admin/users/edit/' . $user->id, ['id' => 'editUserForm']); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="<?= set_value('name', $user->name) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control"
                        value="<?= htmlspecialchars($user->email) ?>" disabled>
                    <div class="form-text">Email cannot be changed.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="<?= set_value('phone', $user->phone) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role_id" id="role_select" class="form-select" required>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->id ?>"
                                <?= (set_value('role_id', $user->role_id) == $role->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role->display_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active"   <?= set_value('status', $user->status) === 'active'   ? 'selected':'' ?>>Active</option>
                        <option value="inactive" <?= set_value('status', $user->status) === 'inactive' ? 'selected':'' ?>>Inactive</option>
                        <option value="banned"   <?= set_value('status', $user->status) === 'banned'   ? 'selected':'' ?>>Banned</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic profile fields -->
            <div id="profile_fields_wrapper" style="display:none;">
                <hr class="my-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="font-weight:600;font-size:15px;">Profile Details</div>
                    <span id="profile_table_badge" class="badge"
                        style="background:#f0f0ff;color:#4f46e5;font-weight:500;font-size:12px;"></span>
                </div>
                <div id="profile_fields_container" class="row g-3"></div>
            </div>

            <div id="profile_loading" style="display:none;" class="mt-3">
                <div class="d-flex align-items-center gap-2 text-muted" style="font-size:13px;">
                    <div class="spinner-border spinner-border-sm"></div>
                    Loading profile fields...
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?= site_url('admin/users/view/' . $user->id) ?>"
                   class="btn btn-outline-secondary">Cancel</a>
            </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
const AJAX_URL      = '<?= site_url('admin/ajax/role-fields/') ?>';

// Existing profile data passed from controller
const existingProfile = <?= json_encode(
    !empty($profile) ? array_diff_key((array)$profile, array_flip(['id','user_id','updated_at','updated_by','last_login','profile_pic'])) : []
) ?>;

// Pre-load on page load with existing values
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('role_select');
    if (sel.value) loadFields(sel.value, existingProfile);
});

document.getElementById('role_select').addEventListener('change', function () {
    // When role changes, load new fields (no pre-fill since it's a different role)
    loadFields(this.value, {});
});

function loadFields(role_id, prefill) {
    const wrapper   = document.getElementById('profile_fields_wrapper');
    const container = document.getElementById('profile_fields_container');
    const loading   = document.getElementById('profile_loading');
    const badge     = document.getElementById('profile_table_badge');

    if (!role_id) {
        wrapper.style.display = 'none';
        container.innerHTML   = '';
        return;
    }

    loading.style.display = 'block';
    wrapper.style.display = 'none';
    container.innerHTML   = '';

    fetch(AJAX_URL + role_id, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        loading.style.display = 'none';

        if (!data.fields || data.fields.length === 0) return;

        badge.textContent = data.table;
        data.fields.forEach(field => {
            const value = prefill[field.name] !== undefined ? prefill[field.name] : '';
            container.innerHTML += buildField(field, value);
        });
        wrapper.style.display = 'block';
    })
    .catch(() => { loading.style.display = 'none'; });
}

function buildField(field, value) {
    const label = field.name.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    const type  = getInputType(field.type);
    const safe  = String(value).replace(/"/g, '&quot;');

    if (type === 'textarea') {
        return `<div class="col-md-6">
            <label class="form-label fw-semibold">${label}</label>
            <textarea name="profile[${field.name}]" class="form-control" rows="2">${safe}</textarea>
        </div>`;
    }

    return `<div class="col-md-6">
        <label class="form-label fw-semibold">${label}</label>
        <input type="${type}" name="profile[${field.name}]"
               class="form-control" value="${safe}">
    </div>`;
}

function getInputType(dbType) {
    dbType = dbType.toLowerCase();
    if (dbType.includes('text'))               return 'textarea';
    if (dbType.includes('int'))                return 'number';
    if (dbType.includes('decimal') ||
        dbType.includes('float'))              return 'number';
    if (dbType === 'date')                     return 'date';
    if (dbType.includes('datetime') ||
        dbType.includes('timestamp'))          return 'datetime-local';
    if (dbType.includes('tinyint(1)'))         return 'checkbox';
    return 'text';
}
</script>

<?php $this->load->view('admin/layouts/footer'); ?>