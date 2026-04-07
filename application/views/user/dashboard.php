<?php $this->load->view('user/layouts/header', ['page_title' => 'Dashboard']); ?>

<div class="row g-4">

    <!-- Left: Profile card -->
    <div class="col-md-4">
        <div class="card p-4 text-center h-100">
            <div style="width:80px;height:80px;background:#e0f2fe;color:#0ea5e9;
                        border-radius:50%;display:flex;align-items:center;justify-content:center;
                        font-size:32px;font-weight:700;margin:0 auto 16px;">
                <?= strtoupper(substr($user->name, 0, 1)) ?>
            </div>
            <div style="font-size:18px;font-weight:700;"><?= htmlspecialchars($user->name) ?></div>
            <div style="font-size:13px;color:#adb5bd;" class="mb-2">
                <?= $this->session->userdata('role_display_name') ?>
            </div>
            <?php if ($user->status === 'active'): ?>
                <span class="badge bg-success-subtle text-success">Active</span>
            <?php elseif ($user->status === 'inactive'): ?>
                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
            <?php else: ?>
                <span class="badge bg-danger-subtle text-danger">Banned</span>
            <?php endif; ?>

            <hr>

            <div class="text-start" style="font-size:13px;">
                <div class="mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-envelope text-muted"></i>
                    <span><?= htmlspecialchars($user->email) ?></span>
                </div>
                <div class="mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-phone text-muted"></i>
                    <span><?= htmlspecialchars($user->phone ?? '—') ?></span>
                </div>
                <div class="mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-calendar text-muted"></i>
                    <span>Joined <?= date('d M Y', strtotime($user->created_at)) ?></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock text-muted"></i>
                    <span>Last login:
                        <?= $profile && $profile->last_login
                            ? date('d M Y, h:i A', strtotime($profile->last_login))
                            : 'First login' ?>
                    </span>
                </div>
            </div>

            <div class="mt-3">
                <a href="<?= site_url('user/profile') ?>" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Right: Profile details -->
    <div class="col-md-8">

        <!-- Account info -->
        <div class="card mb-4">
            <div class="card-header">Account Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">Full Name</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->name) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">Email</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->email) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">Phone</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->phone ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">Role</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($this->session->userdata('role_display_name')) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">Status</div>
                        <div style="font-size:14px;"><?= ucfirst($user->status) ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">Member Since</div>
                        <div style="font-size:14px;"><?= date('d M Y', strtotime($user->created_at)) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic profile fields -->
        <?php
        $profile_arr = $profile ? (array)$profile : [];
        $skip = ['id', 'user_id', 'last_login', 'profile_pic', 'updated_at', 'updated_by'];
        $extra = array_diff_key($profile_arr, array_flip($skip));
        ?>
        <?php if (!empty($extra)): ?>
        <div class="card">
            <div class="card-header">Profile Details</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach ($extra as $key => $val): ?>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">
                            <?= ucwords(str_replace('_', ' ', $key)) ?>
                        </div>
                        <div style="font-size:14px;"><?= htmlspecialchars($val ?? '—') ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $this->load->view('user/layouts/footer'); ?>