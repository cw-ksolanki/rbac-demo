<?php
$uri = $this->uri->segment(2); // e.g. 'dashboard', 'users', 'roles'
?>
<nav class="sidebar-nav">

    <div class="nav-label">Main</div>
    <div class="nav-item">
        <a href="<?= site_url('admin/dashboard') ?>" class="<?= $uri === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
    </div>

    <div class="nav-label mt-2">Manage</div>
    <div class="nav-item">
        <a href="<?= site_url('admin/admins') ?>" class="<?= $uri === 'admins' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> admins
        </a>
    </div>

    <div class="nav-item">
        <a href="<?= site_url('admin/users') ?>" class="<?= $uri === 'users' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Users
        </a>
    </div>
    <div class="nav-item">
        <a href="<?= site_url('admin/roles') ?>" class="<?= $uri === 'roles' ? 'active' : '' ?>">
            <i class="bi bi-shield-check"></i> Roles
        </a>
    </div>
    <div class="nav-item">
        <a href="<?= site_url('admin/material') ?>" class="<?= $uri === 'material' ? 'active' : '' ?>">
            <i class="bi bi-box"></i> material
        </a>
    </div>

</nav>

<div class="sidebar-footer">
    <a href="<?= site_url('auth/logout') ?>">
        <i class="bi bi-box-arrow-left"></i> Logout
    </a>
</div> 