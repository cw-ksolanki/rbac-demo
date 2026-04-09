<?php $this->load->view('user/layouts/header', ['page_title' => 'My Profile']); ?>

<?php $role = $this->session->userdata('role_name'); ?>

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
            <?php if ($user->status === 'active'): ?>
                <span class="badge bg-success-subtle text-success">Active</span>
            <?php elseif ($user->status === 'inactive'): ?>
                <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
            <?php else: ?>
                <span class="badge bg-danger-subtle text-danger">Banned</span>
            <?php endif; ?>
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

        <!-- User Info / Driver Info -->
        <div class="card mb-4">
            <div class="card-header">
                <?= $role === 'driver' ? 'Driver Info' : 'User Info' ?>
            </div>
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
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Company Info (user only) -->
        <?php if ($role === 'user'): ?>
        <div class="card mb-4">
            <div class="card-header">Company Info</div>
            <div class="card-body">
                <?php echo form_open('user/profile/update'); ?>
                <input type="hidden" name="type" value="info">
                <input type="hidden" name="name"  value="<?= htmlspecialchars($user->name) ?>">
                <input type="hidden" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company</label>
                        <input type="text" name="company" class="form-control"
                            value="<?= htmlspecialchars($user->company ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company Website</label>
                        <input type="text" name="company_website" class="form-control"
                            value="<?= htmlspecialchars($user->company_website ?? '') ?>">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Vehicle Info (driver only) -->
        <?php elseif ($role === 'driver'): ?>
        <div class="card mb-4">
            <div class="card-header">Vehicle Info</div>
            <div class="card-body">
                <?php echo form_open('user/profile/update'); ?>
                <input type="hidden" name="type" value="info">
                <input type="hidden" name="name"  value="<?= htmlspecialchars($user->name) ?>">
                <input type="hidden" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vehicle Type</label>
                        <input type="text" name="vehicle_type" class="form-control"
                            value="<?= htmlspecialchars($user->vehicle_type ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vehicle No</label>
                        <input type="text" name="vehicle_no" class="form-control"
                            value="<?= htmlspecialchars($user->vehicle_no ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Licence No</label>
                        <input type="text" name="licence_no" class="form-control"
                            value="<?= htmlspecialchars($user->licence_no ?? '') ?>">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <?php else:?>
            <div class="card mb-4">
            <div class="card-header">Company Info</div>
            <div class="card-body">
                <?php echo form_open('user/profile/update'); ?>
                <input type="hidden" name="type" value="info">
                <input type="hidden" name="name"  value="<?= htmlspecialchars($user->name) ?>">
                <input type="hidden" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company</label>
                        <input type="text" name="company" class="form-control"
                            value="<?= htmlspecialchars($user->company ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Company Website</label>
                        <input type="text" name="company_website" class="form-control"
                            value="<?= htmlspecialchars($user->company_website ?? '') ?>">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Vehicle Info</div>
            <div class="card-body">
                <?php echo form_open('user/profile/update'); ?>
                <input type="hidden" name="type" value="info">
                <input type="hidden" name="name"  value="<?= htmlspecialchars($user->name) ?>">
                <input type="hidden" name="phone" value="<?= htmlspecialchars($user->phone ?? '') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vehicle Type</label>
                        <input type="text" name="vehicle_type" class="form-control"
                            value="<?= htmlspecialchars($user->vehicle_type ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Vehicle No</label>
                        <input type="text" name="vehicle_no" class="form-control"
                            value="<?= htmlspecialchars($user->vehicle_no ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Licence No</label>
                        <input type="text" name="licence_no" class="form-control"
                            value="<?= htmlspecialchars($user->licence_no ?? '') ?>">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php $this->load->view('user/layouts/footer'); ?>