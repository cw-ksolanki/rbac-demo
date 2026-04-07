<?php $this->load->view('user/layouts/header', ['page_title' => 'My Profile']); ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- Left: Info card -->
    <div class="col-md-3">
        <div class="card text-center p-4">
            <div style="width:72px;height:72px;background:#e0f2fe;color:#0ea5e9;
                        border-radius:50%;display:flex;align-items:center;justify-content:center;
                        font-size:28px;font-weight:700;margin:0 auto 12px;">
                <?= strtoupper(substr($user->name, 0, 1)) ?>
            </div>
            <div style="font-weight:600;font-size:16px;"><?= htmlspecialchars($user->name) ?></div>
            <div style="font-size:13px;color:#adb5bd;" class="mb-2">
                <?= $this->session->userdata('role_display_name') ?>
            </div>
            <span class="badge bg-success-subtle text-success">Active</span>
            <hr>
            <div class="text-start" style="font-size:13px;">
                <div class="mb-2">
                    <i class="bi bi-envelope text-muted me-2"></i>
                    <?= htmlspecialchars($user->email) ?>
                </div>
                <div class="mb-2">
                    <i class="bi bi-phone text-muted me-2"></i>
                    <?= htmlspecialchars($user->phone ?? '—') ?>
                </div>
                <div>
                    <i class="bi bi-calendar text-muted me-2"></i>
                    Joined <?= date('d M Y', strtotime($user->created_at)) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Edit forms -->
    <div class="col-md-9">

        <!-- Edit Info + Profile Fields -->
        <div class="card mb-4">
            <div class="card-header">Edit Profile</div>
            <div class="card-body">
                <?php echo form_open('user/profile/update'); ?>
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

                <!-- Dynamic profile fields -->
                <?php if (!empty($role_fields)): ?>
                <hr class="my-3">
                <div class="fw-semibold mb-3" style="font-size:14px;">
                    Additional Details
                </div>
                <div class="row g-3">
                    <?php
                    $profile_arr = $profile ? (array)$profile : [];
                    $skip = ['id','user_id','last_login','profile_pic','updated_at','updated_by'];
                    foreach ($role_fields as $field):
                        $val   = $profile_arr[$field['name']] ?? '';
                        $label = ucwords(str_replace('_', ' ', $field['name']));
                        $type  = strtolower($field['type']);

                        // Determine input type
                        if (strpos($type, 'text') !== false):
                    ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $label ?></label>
                            <textarea name="profile[<?= $field['name'] ?>]"
                                class="form-control" rows="2"><?= htmlspecialchars($val) ?></textarea>
                        </div>
                    <?php   elseif ($type === 'date'): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $label ?></label>
                            <input type="date" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        </div>
                    <?php   elseif (strpos($type, 'datetime') !== false): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $label ?></label>
                            <input type="datetime-local" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        </div>
                    <?php   elseif (strpos($type, 'int') !== false || strpos($type, 'decimal') !== false): ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $label ?></label>
                            <input type="number" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        </div>
                    <?php   else: ?>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= $label ?></label>
                            <input type="text" name="profile[<?= $field['name'] ?>]"
                                class="form-control" value="<?= htmlspecialchars($val) ?>">
                        </div>
                    <?php   endif; ?>
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
                <?php echo form_open('user/profile/update'); ?>
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

<?php $this->load->view('user/layouts/footer'); ?>