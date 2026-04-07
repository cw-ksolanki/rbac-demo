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
                                <?= htmlspecialchars($role->name) ?>
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
    const user = <?= json_encode($user); ?>;
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

        return `<div class="col-md-6">
            <label class="form-label fw-semibold">${label}</label>
            <input type="text" name="${name}" class="form-control" value="${user[name] ?? ''}">
        </div>`;
    }
});
</script>

<?php $this->load->view('admin/layouts/footer'); ?>