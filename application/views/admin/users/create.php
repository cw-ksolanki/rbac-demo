<?php $this->load->view('admin/layouts/header', ['page_title' => 'Create User']); ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Create New User</div>
    <div class="card-body" style="max-width:700px;">
        <?php echo form_open('admin/users/create', ['id' => 'createUserForm']); ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="<?= set_value('name') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                        value="<?= set_value('email') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="<?= set_value('phone') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select name="role_id" id="role_select" class="form-select" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->id ?>"
                                <?= (set_value('role_id') == $role->id) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active"   <?= set_value('status','active')   === 'active'   ? 'selected':'' ?>>Active</option>
                        <option value="inactive" <?= set_value('status','active')   === 'inactive' ? 'selected':'' ?>>Inactive</option>
                        <option value="banned"   <?= set_value('status','active')   === 'banned'   ? 'selected':'' ?>>Banned</option>
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

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePass('password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-text">Minimum 6 characters.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <button type="button" class="btn btn-outline-secondary"
                            onclick="togglePass('confirm_password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>

        <?php echo form_close(); ?>
    </div>
</div>

<script>
    const oldValues = <?= json_encode($_POST ?? []) ?>;
    document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role_select');
    const wrapper    = document.getElementById('profile_fields_wrapper');
    const container  = document.getElementById('profile_fields_container');

    roleSelect.addEventListener('change', handleRoleChange);

    // Run on page load
    handleRoleChange();

    function handleRoleChange() {
        const roleId = parseInt(roleSelect.value);
        container.innerHTML = '';

        let fields = [];

        switch (roleId) {
            case 3: // driver
                fields = [
                    'vehicle_no',
                    'vehicle_type',
                    'licence_no'
                ];
                break;

            case 2: // user
                fields = [
                    'company',
                    'company_website'
                ];
                break;

            case 1: // admin
                fields = [];
                break;

            default: // any other role
                fields = [
                    'vehicle_type',
                    'vehicle_number',
                    'licence_number',
                    'company',
                    'company_website'
                ];
        }

        if (fields.length === 0) {
            wrapper.style.display = 'none';
            return;
        }

        fields.forEach(name => {
            container.innerHTML += buildField(name);
        });

        wrapper.style.display = 'block';
    }

    function buildField(name) {
        const label = name
            .replace(/_/g, ' ')
            .replace(/\b\w/g, c => c.toUpperCase());

        const value = oldValues[name] ?? '';
        return `<div class="col-md-6">
            <label class="form-label fw-semibold">${label}</label>
            <input type="text" name="${name}" class="form-control" value="${value}">
        </div>`;
    }
});
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.innerHTML = isPass
        ? '<i class="bi bi-eye-slash"></i>'
        : '<i class="bi bi-eye"></i>';
}

// If form re-rendered after error — re-trigger role load
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('role_select');
    if (sel.value) sel.dispatchEvent(new Event('change'));
});
</script>

<?php $this->load->view('admin/layouts/footer'); ?>