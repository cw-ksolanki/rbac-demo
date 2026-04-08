<?php $this->load->view('user/layouts/header', ['page_title' => 'Dashboard']); ?>

<?php $role = $this->session->userdata('role_name'); ?>

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
            <div class="mt-3">
                <a href="<?= site_url('user/profile') ?>" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Right: Role-based info -->
    <div class="col-md-8">

        <?php if ($role === 'user'): ?>
        <div class="card">
            <div class="card-header">Company Info</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#adb5bd;">Company</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->company ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#adb5bd;">Company Website</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->company_website ?? '—') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif ($role === 'driver'): ?>
        <div class="card">
            <div class="card-header">Vehicle Info</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#adb5bd;">Vehicle Type</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->vehicle_type ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#adb5bd;">Vehicle No</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->vehicle_no ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:#adb5bd;">Licence No</div>
                        <div style="font-size:14px;"><?= htmlspecialchars($user->licence_no ?? '—') ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $this->load->view('user/layouts/footer'); ?>