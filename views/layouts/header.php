<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hệ thống Quản lý Điểm' ?></title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- ApexCharts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
    
    <style>
        :root {
            --primary-color: #435ebe;
            --primary-dark: #25396f;
            --sidebar-bg: #ffffff;
            --sidebar-width: 260px;
            --sidebar-color: #607080;
            --sidebar-active-bg: #435ebe;
            --sidebar-active-color: #fff;
            --bg-color: #f2f7ff;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: #2c3e50;
        }

        /* Layout */
        #wrapper {
            display: flex;
            width: 100%;
            overflow-x: hidden;
            position: relative;
        }

        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            color: var(--sidebar-color);
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            flex-shrink: 0; /* Prevent shrinking */
        }

        #sidebar.active {
            margin-left: calc(var(--sidebar-width) * -1);
        }

        #content {
            flex: 1; /* Take remaining space */
            width: auto; /* Allow resizing */
            min-width: 0; /* Fix for flexbox child overflow */
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(var(--sidebar-width) * -1); /* Hidden by default on mobile */
                position: fixed;
                height: 100%;
            }
            
            #sidebar.active {
                margin-left: 0; /* Show when active */
                box-shadow: 0 0 50px rgba(0,0,0,0.5);
            }
            
            #content {
                width: 100%;
                flex: none;
            }
            
            /* Overlay when sidebar is active on mobile */
            #sidebar.active + #content::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.3);
                z-index: 999;
                pointer-events: auto; /* Block clicks */
            }
        }

        /* Navbar */
        .navbar {
            background: #fff !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.8rem 1.5rem;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.25rem;
        }

        /* Sidebar Links */
        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .sidebar-header h3 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }

        .sidebar-menu {
            padding: 1.5rem 1rem;
            flex-grow: 1;
        }

        .sidebar-menu .nav-link {
            padding: 0.8rem 1rem;
            color: var(--sidebar-color);
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            font-weight: 500;
            transition: all 0.3s;
        }

        .sidebar-menu .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .sidebar-menu .nav-link:hover {
            color: var(--primary-color);
            background-color: #f0f2f5;
        }

        .sidebar-menu .nav-link.active {
            background-color: var(--sidebar-active-bg);
            color: var(--sidebar-active-color);
            box-shadow: 0 5px 10px rgba(67, 94, 190, 0.3);
        }

        /* Cards & UI */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            background: #fff;
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1.2rem 1.5rem;
            font-weight: 600;
            color: #25396f;
        }
        
        .card-body {
            padding: 1.5rem;
        }

        .btn {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Toast */
        .toast-container {
            z-index: 1060;
        }
    </style>
</head>
<body>

<?php if (Auth::check()): ?>
    <div id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><i class="bi bi-mortarboard-fill"></i> QL Điểm</h3>
            </div>
            <div class="sidebar-menu">
                <?php require __DIR__ . '/sidebar.php'; ?>
            </div>
            <div class="p-3 border-top">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div style="line-height: 1.2;">
                        <div class="fw-bold text-truncate" style="max-width: 120px;"><?= htmlspecialchars(Auth::name()) ?></div>
                        <small class="text-muted"><?= ucfirst(str_replace('_', ' ', Auth::role())) ?></small>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-light border-0">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <span class="d-none d-md-block">Xin chào, <strong><?= htmlspecialchars(Auth::name()) ?></strong></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                <li><a class="dropdown-item" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <div class="main-content p-4">
<?php else: ?>
    <!-- Navbar for Non-Auth Pages -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= url('/') ?>">
                <i class="bi bi-mortarboard-fill"></i> Quản lý Điểm
            </a>
            <div class="ms-auto">
                 <a href="<?= url('/login') ?>" class="btn btn-light text-primary fw-bold">Đăng nhập</a>
            </div>
        </div>
    </nav>
<?php endif; ?>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toastSuccess" class="toast" role="alert"><div class="toast-header bg-success text-white"><strong class="me-auto">Thành công</strong><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button></div><div class="toast-body" id="toastSuccessBody"></div></div>
    <div id="toastError" class="toast" role="alert"><div class="toast-header bg-danger text-white"><strong class="me-auto">Lỗi</strong><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button></div><div class="toast-body" id="toastErrorBody"></div></div>
    <div id="toastWarning" class="toast" role="alert"><div class="toast-header bg-warning text-dark"><strong class="me-auto">Cảnh báo</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div><div class="toast-body" id="toastWarningBody"></div></div>
    <div id="toastInfo" class="toast" role="alert"><div class="toast-header bg-info text-white"><strong class="me-auto">Thông tin</strong><button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button></div><div class="toast-body" id="toastInfoBody"></div></div>
</div>

