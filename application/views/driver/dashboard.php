<?php $this->load->view('driver/layouts/header', ['page_title' => 'Dashboard']); ?>

<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- Left: Profile card -->
    <div class="col-md-4">
        <div class="card p-4 text-center">
            <div style="width:80px;height:80px;background:#fef3c7;color:#d97706;
                        border-radius:50%;display:flex;align-items:center;justify-content:center;
                        font-size:32px;font-weight:700;margin:0 auto 16px;">
                <?= strtoupper(substr($user->name, 0, 1)) ?>
            </div>
            <div style="font-size:18px;font-weight:700;"><?= htmlspecialchars($user->name) ?></div>
            <div style="font-size:13px;color:#adb5bd;" class="mb-2">Driver</div>

            <!-- Availability badge -->
            <?php
            $avail       = $profile->availability ?? 'offline';
            $avail_color = match($avail) {
                'online'  => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'dot' => '#22c55e'],
                'on_trip' => ['bg' => '#eff6ff', 'text' => '#2563eb', 'dot' => '#3b82f6'],
                default   => ['bg' => '#f9fafb', 'text' => '#6b7280', 'dot' => '#9ca3af'],
            };
            ?>
            <div style="display:inline-flex;align-items:center;gap:6px;
                        background:<?= $avail_color['bg'] ?>;color:<?= $avail_color['text'] ?>;
                        padding:5px 12px;border-radius:20px;font-size:13px;font-weight:600;">
                <span style="width:8px;height:8px;border-radius:50%;
                             background:<?= $avail_color['dot'] ?>;display:inline-block;"></span>
                <?= ucfirst(str_replace('_', ' ', $avail)) ?>
            </div>

            <hr>
            <div class="text-start" style="font-size:13px;">
                <div class="mb-2 d-flex gap-2"><i class="bi bi-envelope text-muted"></i><?= htmlspecialchars($user->email) ?></div>
                <div class="mb-2 d-flex gap-2"><i class="bi bi-phone text-muted"></i><?= htmlspecialchars($user->phone ?? '—') ?></div>
                <div class="mb-2 d-flex gap-2"><i class="bi bi-calendar text-muted"></i>Joined <?= date('d M Y', strtotime($user->created_at)) ?></div>
                <div class="d-flex gap-2"><i class="bi bi-clock text-muted"></i>
                    Last login: <?= $profile && $profile->last_login ? date('d M Y, h:i A', strtotime($profile->last_login)) : 'First login' ?>
                </div>
            </div>
            <div class="mt-3">
                <a href="<?= site_url('driver/profile') ?>" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Right -->
    <div class="col-md-8 d-flex flex-column gap-4">

        <!-- Availability toggle -->
        <div class="card">
            <div class="card-header">Change Availability</div>
            <div class="card-body">
                <?php echo form_open('driver/profile/update'); ?>
                <input type="hidden" name="type" value="availability">
                <div class="d-flex gap-3 flex-wrap">
                    <?php foreach (['online' => ['#f0fdf4','#16a34a','#22c55e'], 'offline' => ['#f9fafb','#6b7280','#9ca3af'], 'on_trip' => ['#eff6ff','#2563eb','#3b82f6']] as $status => $colors): ?>
                    <label style="cursor:pointer;">
                        <input type="radio" name="availability" value="<?= $status ?>"
                            <?= ($avail === $status) ? 'checked' : '' ?>
                            style="display:none;" class="avail-radio">
                        <div class="avail-btn" style="
                            padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
                            border: 2px solid <?= ($avail === $status) ? $colors[1] : '#e9ecef' ?>;
                            background: <?= ($avail === $status) ? $colors[0] : '#fff' ?>;
                            color: <?= ($avail === $status) ? $colors[1] : '#6c757d' ?>;
                            display: flex; align-items: center; gap: 8px; transition: all 0.15s;">
                            <span style="width:8px;height:8px;border-radius:50%;background:<?= $colors[2] ?>;display:inline-block;"></span>
                            <?= ucfirst(str_replace('_', ' ', $status)) ?>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">Update Availability</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>

        <!-- Driver info -->
        <div class="card">
            <div class="card-header">Driver Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $skip = ['id','user_id','last_login','profile_pic','updated_at','updated_by','availability'];
                    $profile_arr = $profile ? (array)$profile : [];
                    $shown = false;
                    foreach ($profile_arr as $key => $val):
                        if (in_array($key, $skip)) continue;
                        $shown = true;
                    ?>
                    <div class="col-md-6">
                        <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                    letter-spacing:0.5px;color:#adb5bd;">
                            <?= ucwords(str_replace('_', ' ', $key)) ?>
                        </div>
                        <div style="font-size:14px;"><?= htmlspecialchars($val ?? '—') ?></div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!$shown): ?>
                        <div class="text-muted" style="font-size:13px;">No profile details yet.
                            <a href="<?= site_url('driver/profile') ?>">Fill your profile</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Highlight selected availability option visually
document.querySelectorAll('.avail-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.avail-radio').forEach(r => {
            const btn = r.nextElementSibling;
            btn.style.border = '2px solid #e9ecef';
            btn.style.background = '#fff';
            btn.style.color = '#6c757d';
        });
        const btn = this.nextElementSibling;
        const colors = {
            online:  ['#f0fdf4', '#16a34a'],
            offline: ['#f9fafb', '#6b7280'],
            on_trip: ['#eff6ff', '#2563eb'],
        };
        const c = colors[this.value];
        btn.style.border     = `2px solid ${c[1]}`;
        btn.style.background = c[0];
        btn.style.color      = c[1];
    });
});
</script>

<?php $this->load->view('driver/layouts/footer'); ?>