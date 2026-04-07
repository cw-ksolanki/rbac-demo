<?php $uri = $this->uri->segment(2); ?>
<nav class="sidebar-nav">
    <div class="nav-label">Main</div>
    <div class="nav-item">
        <a href="<?= site_url('user/dashboard') ?>"
           class="<?= $uri === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
    </div>

    <div class="nav-label mt-2">Account</div>
    <div class="nav-item">
        <a href="<?= site_url('user/profile') ?>"
           class="<?= $uri === 'profile' ? 'active' : '' ?>">
            <i class="bi bi-person"></i> My Profile
        </a>
    </div>
</nav>

<div class="sidebar-footer">
    <a href="<?= site_url('auth/logout') ?>">
        <i class="bi bi-box-arrow-left"></i> Logout
    </a>
</div>