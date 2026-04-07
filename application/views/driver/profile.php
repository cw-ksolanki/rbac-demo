<?php $this->load->view('driver/layouts/header', ['page_title' => 'My Profile']); ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<div class="row g-4">
    <!-- Left: Info card -->
    <div class="col-md-3">
        <div class="card p-4 text-center">
            <div style="width:72px;height:72px;background:#fef3c7;color:#d97706;
                        border-radius:50%;display:flex;align-items:center;justify-content:center;
                        font-size:28px;font-weight:700;margin:0 auto 12px;">
                <?= strtoupper(substr($user->name, 0, 1)) ?>
            </div>
            <div style="font-weight:600;font-size:16px;"><?= htmlspecialchars($user->name) ?></div>
            <div style="font-size:13px;color:#adb5bd;" class="mb-2">Driver</div>
            <?php if ($user->status === 'active'): ?>
                <span class="badge bg-success-subtle text-success">Active</span>
            <?php else: ?>
                <span class="badge bg-secondary-subtle text-secondary"><?= ucfirst($user->status) ?></span>
            <?php endif; ?>
            <hr>
            <div class="text-start" style="font-size:13px;">
                <div class="mb-2"><i class="bi bi-envelope text-muted me-2"></i><?= htmlspecialchars($user->email) ?></div>
                <div class="mb-2"><i class="bi bi-phone text-muted me-2"></i><?= htmlspecialchars($user->phone ?? '—') ?></div>
                <div><i class="bi bi-calendar text-muted me-2"></i>Joined <?= date('d M Y', strtotime($user->created_at)) ?></div>
            </div>
        </div>
    </div>

    <!-- Right: Forms -->
    <div class="col-md-9">

        <!-- Edit Info -->
        <div class="card mb-4">
            <div class="card-header">Edit Profile</div>
            <div class="card-body">
                <?php echo form_open('driver/profile/update'); ?>
                <input type="hidden" name="type" value="info">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control"
                            value="<?= htmlspecialchars($user->name) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone</label>
                        <input type="text" name="phone" class="form-control"
                            value="<?= htmlspecialchars($user->phone ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control"
                            value="<?= htmlspecialchars($user->email) ?>" disabled>
                        <div class="form-text">Email cannot be changed.</div>
                    </div>
                </div>

                <?php if (!empty($role_fields)): ?>
                <hr class="my-3">
                <div class="fw-semibold mb-3" style="font-size:14px;">Driver Details</div>
                <div class="row g-3">
                    <?php
                    $profile_arr = $profile ? (array)$profile : [];
                    $skip_edit   = ['availability'];
                    foreach ($role_fields as $field):
                        if (in_array($field['name'], $skip_edit)) continue;
                        $val   = $profile_arr[$field['name']] ?? '';
                        $label = ucwords(str_replace('_', ' ', $field['name']));
                        $type  = strtolower($field['type']);
                    ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"><?= $label ?></label>
                        <?php if (strpos($type, 'text') !== false): ?>
                            <textarea name="profile[<?= $field['name'] ?>]"
                                class="form-control" rows="2"><?= htmlspecialchars($val) ?></textarea>
                        <?php elseif ($type === 'date'): ?>
                            <input type="date" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        <?php elseif (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false): ?>
                            <input type="number" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        <?php else: ?>
                            <input type="text" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <?php echo form_open('driver/profile/update'); ?>
                <input type="hidden" name="type" value="password">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('driver/layouts/footer'); ?>