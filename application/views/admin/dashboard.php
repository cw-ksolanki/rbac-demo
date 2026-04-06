<?php $this->load->view('admin/layouts/header', ['page_title' => 'Dashboard']); ?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:#eef2ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-people text-primary fs-5"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Users</div>
                    <div style="font-size:22px;font-weight:700;"><?= $total_users ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person-check text-success fs-5"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Active Users</div>
                    <div style="font-size:22px;font-weight:700;"><?= $active_users ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:#fff7ed;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-truck text-warning fs-5"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Drivers</div>
                    <div style="font-size:22px;font-weight:700;"><?= $total_drivers ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:44px;height:44px;background:#fdf4ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-shield-check" style="color:#a855f7;font-size:18px;"></i>
                </div>
                <div>
                    <div class="text-muted" style="font-size:12px;">Total Roles</div>
                    <div style="font-size:22px;font-weight:700;"><?= $total_roles ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Recent Users</div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($recent_users)): ?>
                <?php foreach ($recent_users as $u): ?>
                <tr>
                    <td class="ps-4"><?= htmlspecialchars($u->name) ?></td>
                    <td><?= htmlspecialchars($u->email) ?></td>
                    <td><?= htmlspecialchars($u->role_display_name ?? $u->role_name) ?></td>
                    <td>
                        <?php if ($u->status === 'active'): ?>
                            <span class="badge bg-success-subtle text-success">Active</span>
                        <?php elseif ($u->status === 'inactive'): ?>
                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                        <?php else: ?>
                            <span class="badge bg-danger-subtle text-danger">Banned</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d M Y', strtotime($u->created_at)) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted py-4">No users yet</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('admin/layouts/footer'); ?>