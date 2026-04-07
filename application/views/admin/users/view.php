<?php $this->load->view('admin/layouts/header', [$page_title]); ?>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card text-center p-4">
            <div style="width:72px;height:72px;background:#eef2ff;color:#4f46e5;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;
                        font-size:28px;font-weight:700;margin:0 auto 12px;">
                <?= strtoupper(substr($user->name, 0, 1)) ?>
            </div>
            <div style="font-weight:600;font-size:16px;"><?= htmlspecialchars($user->name) ?></div>
            <div class="mb-2">
                <span class="badge" style="background:#f0f0ff;color:#4f46e5;">
                    <?= htmlspecialchars($user->role_display_name ?? $user->role_name) ?>
                </span>
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
                <div class="mb-2"><i class="bi bi-envelope text-muted me-2"></i><?= htmlspecialchars($user->email) ?></div>
                <div class="mb-2"><i class="bi bi-phone text-muted me-2"></i><?= htmlspecialchars($user->phone ?? '—') ?></div>
                <div class="mb-2"><i class="bi bi-calendar text-muted me-2"></i>Joined <?= date('d M Y', strtotime($user->created_at)) ?></div>
            </div>

            <?php if ($user->role_name !== 'admin'): ?>
            <div class="mt-3 d-grid gap-2">
                <a href="<?= site_url('admin/users/edit/' . $user->id) ?>" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-1"></i> Edit User
                </a>
                <button class="btn btn-outline-danger btn-sm"
                    onclick="confirmDelete(<?= $user->id ?>, '<?= htmlspecialchars($user->name) ?>')">
                    <i class="bi bi-trash me-1"></i> Delete User
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Profile Details</div>
            <div class="card-body">
                <?php if ($user): ?>
                    <?php      
                    $skip = ['id', 'password'];
                    if($user->role_name === 'driver'){
                        array_push($skip,'company');
                        array_push($skip,'company_website');
                    }
                    if($user->role_name === 'user'){
                        array_push($skip,'vehicle_type');
                        array_push($skip,'vehicle_no');
                        array_push($skip,'licence_no');
                    }
                    $profile_arr = (array) $user;
                    ?>
                    <div class="row g-3">
                    <?php foreach ($profile_arr as $key => $val): ?>
                        <?php if (in_array($key, $skip)) continue; ?>
                        <div class="col-md-6">
                            <div style="font-size:11px;font-weight:600;text-transform:uppercase;
                                        letter-spacing:0.5px;color:#adb5bd;">
                                <?= ucwords(str_replace('_', ' ', $key)) ?>
                            </div>
                            <div style="font-size:14px;color:#212529;">
                                <?= htmlspecialchars($val ?? '—') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">No profile data found for this user.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Back button -->
<div class="mt-3">
    <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Users
    </a>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Delete User</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-0">Are you sure you want to delete <strong id="userName"></strong>?</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteBtn" href="#" class="btn btn-sm btn-danger">Yes, Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('userName').textContent = name;
    document.getElementById('deleteBtn').href = '<?= site_url('admin/users/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php $this->load->view('admin/layouts/footer'); ?>