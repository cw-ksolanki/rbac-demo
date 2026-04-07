<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' — Dashboard' : 'Dashboard' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; margin: 0; }

        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e9ecef;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 20px 24px;
            font-size: 18px;
            font-weight: 700;
            color: #0ea5e9;
            border-bottom: 1px solid #e9ecef;
        }
        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #adb5bd;
            letter-spacing: 1px;
            padding: 8px 12px 4px;
        }
        .nav-item a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 7px;
            color: #495057;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
        }
        .nav-item a:hover  { background: #f0f9ff; color: #0ea5e9; }
        .nav-item a.active { background: #e0f2fe; color: #0ea5e9; font-weight: 600; }
        .nav-item a i { font-size: 16px; }
        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid #e9ecef;
        }
        .sidebar-footer a {
            color: #dc3545;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 7px;
            font-size: 14px;
            transition: background 0.15s;
        }
        .sidebar-footer a:hover { background: #fff5f5; }

        #main {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #topbar {
            background: #ffffff;
            border-bottom: 1px solid #e9ecef;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        .topbar-title { font-size: 16px; font-weight: 600; color: #212529; }
        .topbar-user a {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #495057;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 8px;
            transition: background 0.15s;
        }
        .topbar-user a:hover { background: #f0f9ff; color: #0ea5e9; }
        .topbar-user .avatar {
            width: 34px; height: 34px;
            background: #e0f2fe;
            color: #0ea5e9;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 13px;
            flex-shrink: 0;
        }
        .page-content { padding: 28px; flex: 1; }

        .card { border: 1px solid #e9ecef; border-radius: 10px; box-shadow: none; }
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 16px 20px;
            font-weight: 600;
            font-size: 15px;
            border-radius: 10px 10px 0 0 !important;
        }
        .alert { border-radius: 8px; font-size: 14px; }
        .btn-primary { background: #0ea5e9; border-color: #0ea5e9; }
        .btn-primary:hover { background: #0284c7; border-color: #0284c7; }
        .table th { font-size: 13px; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.4px; }
        .table td { font-size: 14px; vertical-align: middle; }
    </style>
</head>
<body>
<div id="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-person-circle me-2"></i>My Panel
    </div>
    <?php $this->load->view('user/layouts/sidebar'); ?>
</div>

<div id="main">
    <div id="topbar">
        <span class="topbar-title"><?= isset($page_title) ? $page_title : 'Dashboard' ?></span>
        <div class="topbar-user">
            <a href="<?= site_url('user/profile') ?>">
                <div class="avatar">
                    <?= strtoupper(substr($this->session->userdata('user_name'), 0, 1)) ?>
                </div>
                <div>
                    <div style="font-weight:600;line-height:1.2;"><?= $this->session->userdata('user_name') ?></div>
                    <div style="font-size:11px;color:#adb5bd;"><?= $this->session->userdata('role_display_name') ?></div>
                </div>
                <i class="bi bi-chevron-down" style="font-size:11px;color:#adb5bd;"></i>
            </a>
        </div>
    </div>
    <div class="page-content">