<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= !empty($title) ? esc($title) . ' - SIM-INKINDO DKI' : 'SIM-INKINDO DKI - Sistem Informasi & Manajemen INKINDO DKI' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --inkindo-blue: #0A64A4;
            --inkindo-green: #62B845;
            --bg-app: #F0F3F7;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --white: #FFFFFF;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-app);
            color: var(--text-dark);
            overflow-x: hidden;
        }
        .main-content-wrapper {
            background-color: var(--white);
            border-radius: 30px;
            margin: 15px 15px 15px 0;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
            min-height: calc(100vh - 30px);
        }
        .navbar-custom {
            background-color: transparent;
        }
        .navbar-custom .navbar-brand, .navbar-custom .nav-link {
            color: var(--text-dark);
            font-weight: 600;
        }
        
        /* Sidebar Styles */
        .sidebar {
            background-color: transparent;
            color: var(--text-dark);
            flex: 0 0 270px;
            width: 270px;
            max-width: 270px;
            min-height: 100vh;
            padding: 10px 5px;
        }
        .sidebar-header {
            padding: 25px 25px 10px;
            text-align: left;
        }
        .user-panel {
            padding: 15px 25px;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .user-panel img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
            border: 2px solid var(--white);
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .user-panel .info {
            color: var(--text-dark);
        }
        .user-panel .info p {
            margin: 0;
            font-weight: 600;
            font-size: 14px;
        }
        .user-panel .info small {
            font-size: 11px;
            color: var(--text-muted);
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .header-nav {
            padding: 15px 25px 5px;
            font-size: 11px;
            color: var(--text-muted);
            background: transparent;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .sidebar-menu > li > a {
            padding: 12px 20px;
            margin: 4px 15px;
            display: flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 500;
            border-radius: 14px;
            transition: all 0.2s ease;
        }
        .sidebar-menu > li > a:hover {
            background: rgba(0,0,0,0.03);
            color: var(--text-dark);
        }
        .sidebar-menu > li > a.active, .sidebar-menu > li > a[aria-expanded="true"] {
            background: var(--white);
            color: var(--text-dark);
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03), 0 2px 4px rgba(0,0,0,0.02);
        }
        /* Update icon color for active item to Inkindo Blue */
        .sidebar-menu > li > a.active i, .sidebar-menu > li > a[aria-expanded="true"] i {
            color: var(--inkindo-blue);
        }
        .sidebar-menu .treeview-menu {
            list-style: none;
            padding: 0;
            background: transparent;
        }
        .sidebar-menu .treeview-menu > li > a {
            padding: 10px 15px 10px 50px;
            margin: 2px 15px;
            display: flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13.5px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        .sidebar-menu .treeview-menu > li > a:hover, .sidebar-menu .treeview-menu > li > a.active {
            color: var(--inkindo-blue);
            background: rgba(10, 100, 164, 0.05);
            font-weight: 600;
        }
        .dropdown-toggle::after {
            margin-left: auto;
            vertical-align: middle;
            transition: transform 0.3s;
        }
        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(-180deg);
        }
        
        /* Custom Table Styles (Floating Rows) */
        .table-custom-wrap {
            background: transparent;
        }
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100%;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        .table-custom thead th {
            border: none;
            color: var(--text-muted);
            font-weight: 700;
            font-size: 13px;
            padding: 0 20px 10px;
            text-transform: capitalize;
            background: transparent;
        }
        .table-custom tbody tr {
            background-color: var(--white);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .table-custom tbody tr td {
            border: none;
            padding: 16px 20px;
            vertical-align: middle;
            font-size: 14px;
            color: var(--text-dark);
            background-color: transparent !important;
            transition: all 0.3s ease;
        }
        .table-custom tbody tr td:first-child {
            border-radius: 12px 0 0 12px;
            font-weight: 600;
        }
        .table-custom tbody tr td:last-child {
            border-radius: 0 12px 12px 0;
        }
        .table-custom tbody tr:hover {
            background-color: var(--inkindo-blue) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(10, 100, 164, 0.25);
        }
        .table-custom tbody tr:hover td,
        .table-custom tbody tr:hover td .text-dark,
        .table-custom tbody tr:hover td .text-muted,
        .table-custom tbody tr:hover td .text-secondary,
        .table-custom tbody tr:hover td .text-warning-emphasis {
            color: var(--white) !important;
        }
        .table-custom tbody tr:hover td .badge {
            background-color: rgba(255,255,255,0.2) !important;
            color: var(--white) !important;
            border-color: rgba(255,255,255,0.35) !important;
        }
        .table-custom tbody tr:hover td .btn {
            background-color: var(--white) !important;
            color: var(--inkindo-blue) !important;
            border-color: var(--white) !important;
        }
        .badge-soft-primary { background-color: rgba(10, 100, 164, 0.1); color: var(--inkindo-blue); font-weight: 600; }
        .badge-soft-success { background-color: rgba(98, 184, 69, 0.1); color: var(--inkindo-green); font-weight: 600; }
        .badge-soft-warning { background-color: rgba(255, 193, 7, 0.15); color: #b78a06; font-weight: 600; }
        .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; font-weight: 600; }
        .badge-soft-secondary { background-color: #f1f5f9; color: var(--text-muted); font-weight: 600; }

        .btn-inkindo {
            background-color: var(--inkindo-blue);
            color: var(--white);
            border: 1px solid var(--inkindo-blue);
            font-weight: 600;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(10, 100, 164, 0.2);
            transition: all 0.2s ease;
        }
        .btn-inkindo:hover, .btn-inkindo:focus, .btn-inkindo:active {
            background-color: #085287 !important;
            border-color: #085287 !important;
            color: var(--white) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(10, 100, 164, 0.3);
        }

        /* Mobile Drawer & Responsive Enhancements */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            z-index: 1050;
            display: none;
        }
        .sidebar-backdrop.show {
            display: block;
        }
        .sidebar-toggle-btn {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
        }
        .sidebar-close-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                flex: none;
                width: 270px;
                max-width: 82vw;
                height: 100vh;
                min-height: 100vh;
                overflow-y: auto;
                background-color: var(--bg-app);
                z-index: 1055;
                transform: translateX(-105%);
                transition: transform 0.28s ease, box-shadow 0.28s ease;
                padding: 12px 8px 28px;
            }
            .sidebar.is-open {
                transform: translateX(0);
                box-shadow: 0 12px 32px rgba(15, 23, 42, 0.22);
            }
            .sidebar-header {
                padding: 18px 20px 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .main-content-wrapper {
                width: 100%;
                min-width: 0;
                margin: 0;
                border-radius: 0;
                min-height: 100vh;
                box-shadow: none;
            }
            .top-app-navbar {
                border-bottom: 1px solid #f1f5f9;
            }
            .table-custom-wrap .table-responsive,
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 6px;
            }
        }
        @media (max-width: 767.98px) {
            .top-app-navbar .navbar-brand {
                font-size: 17px !important;
            }
            .top-app-navbar small {
                font-size: 11px !important;
            }
            .table-custom {
                border-spacing: 0 8px;
            }
            .table-custom thead th {
                font-size: 11px;
                padding: 8px 12px;
                white-space: nowrap;
            }
            .table-custom tbody tr td {
                padding: 12px;
                font-size: 13px;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left !important;
                margin-bottom: 10px;
            }
            .dataTables_wrapper .dataTables_filter label {
                width: 100%;
                display: block;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                max-width: 100%;
                margin-left: 0 !important;
                margin-top: 6px;
                display: block;
            }
            .dataTables_wrapper .dataTables_info {
                text-align: center !important;
                margin-bottom: 10px;
                font-size: 12.5px;
            }
            .dataTables_wrapper .dataTables_paginate .pagination {
                justify-content: center !important;
                flex-wrap: wrap;
                gap: 4px;
            }
            .row > [class*="col-md-"].border-start {
                border-left: 0 !important;
                border-top: 1px solid #e2e8f0;
                padding-top: 14px;
                margin-top: 14px;
            }
            .row > [class*="col-md-"] > .ms-2 {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>

    <?php if(session()->get('logged_in')): ?>
    <div class="d-flex" style="min-height: 100vh;">
        <div class="sidebar-backdrop d-lg-none" id="mobileSidebarBackdrop"></div>

        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column" id="appSidebar">
            <div class="sidebar-header">
                <img src="/logo-inkindo.png" alt="INKINDO DKI" style="max-width: 65%; height: auto;">
                <button type="button" class="btn btn-sm btn-light border rounded-circle d-lg-none sidebar-close-btn flex-shrink-0" id="mobileSidebarClose" aria-label="Tutup Menu">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            
            <div class="user-panel">
                <img src="https://ui-avatars.com/api/?name=<?= session()->get('username'); ?>&background=random&color=fff&size=100" alt="User Image">
                <div class="info">
                    <p><?= session()->get('username'); ?></p>
                    <small><i class="bi bi-circle-fill text-success" style="font-size: 8px; margin-right: 3px;"></i> Online</small>
                </div>
            </div>

            <ul class="sidebar-menu flex-column mb-auto">
                <li class="header-nav">MAIN NAVIGATION</li>
                
                <?php if(session()->get('role') === 'admin'): ?>
                <li>
                    <a href="/dashboard" class="<?= (uri_string() == 'dashboard') ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 me-2 text-center" style="width: 20px;"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="/pencarian" class="<?= (strpos(uri_string(), 'pencarian') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-search me-2 text-center" style="width: 20px;"></i> <span>Pencarian</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if(in_array(session()->get('role'), ['admin', 'user'], true)): ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#anggotaCollapse" aria-expanded="<?= (strpos(uri_string(), 'perusahaan') === 0 || strpos(uri_string(), 'sbu') === 0) ? 'true' : 'false' ?>">
                        <i class="bi bi-person-badge-fill me-2 text-center" style="width: 20px;"></i> <span>Anggota</span>
                    </a>
                    <ul class="treeview-menu collapse <?= (strpos(uri_string(), 'perusahaan') === 0 || strpos(uri_string(), 'sbu') === 0) ? 'show' : '' ?>" id="anggotaCollapse">
                        <li>
                            <a href="/perusahaan" class="<?= (strpos(uri_string(), 'perusahaan') === 0) ? 'active' : '' ?>">
                                <i class="bi bi-building me-2"></i> Data Perusahaan
                            </a>
                        </li>
                        <li>
                            <a href="/sbu" class="<?= (strpos(uri_string(), 'sbu') === 0) ? 'active' : '' ?>">
                                <i class="bi bi-card-checklist me-2"></i> Data SBU
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

                <?php if(in_array(session()->get('role'), ['admin', 'mitra'], true)): ?>
                <?php
                    $dbNav = \Config\Database::connect();
                    $pendingMitraCount = 0;
                    $unreadMsgCount = 0;
                    if (session()->get('role') === 'admin') {
                        $pendingMitraCount = (int) $dbNav->table('mitra_kerja')->where('status_kemitraan', 'pending')->countAllResults();
                        $unreadMsgCount = (int) $dbNav->table('mitra_messages')->where('is_read', 0)->countAllResults();
                    } else {
                        $unreadMsgCount = (int) $dbNav->table('mitra_messages m')
                            ->join('mitra_kerja k', 'k.id = m.mitra_id', 'inner')
                            ->where('k.user_id', session()->get('id'))
                            ->where('m.is_read', 0)
                            ->countAllResults();
                    }
                ?>
                <li>
                    <a href="/mitra" class="<?= (uri_string() === 'mitra' || strpos(uri_string(), 'mitra/create') === 0 || strpos(uri_string(), 'mitra/edit') === 0 || strpos(uri_string(), 'mitra/products') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-people-fill me-2 text-center" style="width: 20px;"></i>
                        <span>Data Mitra Kerja</span>
                        <?php if($pendingMitraCount > 0): ?>
                            <span class="badge rounded-pill bg-danger ms-auto" style="font-size: 11px;"><?= $pendingMitraCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="/mitra/messages" class="<?= (strpos(uri_string(), 'mitra/messages') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-envelope-paper-fill me-2 text-center" style="width: 20px;"></i>
                        <span>Pesan Masuk</span>
                        <?php if($unreadMsgCount > 0): ?>
                            <span class="badge rounded-pill bg-primary ms-auto" style="font-size: 11px;"><?= $unreadMsgCount; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if(session()->get('role') === 'admin'): ?>
                <?php
                    $isCmsRoute = (strpos(uri_string(), 'cms') === 0);
                    $isCmsHomepage = (strpos(uri_string(), 'cms/homepage') === 0 || strpos(uri_string(), 'cms/banner') === 0);
                    $isCmsTentang = (strpos(uri_string(), 'cms/tentang') === 0);
                    $isCmsRegulasi = (strpos(uri_string(), 'cms/regulasi') === 0);
                    $isCmsLelang = (strpos(uri_string(), 'cms/lelang') === 0);
                    $isCmsBerita = ($isCmsRoute && !$isCmsHomepage && !$isCmsTentang && !$isCmsRegulasi && !$isCmsLelang);
                ?>
                <li>
                    <a href="#" class="dropdown-toggle" data-bs-toggle="collapse" data-bs-target="#cmsCollapse" aria-expanded="<?= $isCmsRoute ? 'true' : 'false' ?>">
                        <i class="bi bi-window-sidebar me-2 text-center" style="width: 20px;"></i> <span>Admin CMS</span>
                    </a>
                    <ul class="treeview-menu collapse <?= $isCmsRoute ? 'show' : '' ?>" id="cmsCollapse">
                        <li>
                            <a href="/cms" class="<?= $isCmsBerita ? 'active' : '' ?>">
                                <i class="bi bi-newspaper me-2"></i> Berita &amp; Informasi
                            </a>
                        </li>
                        <li>
                            <a href="/cms/homepage" class="<?= $isCmsHomepage ? 'active' : '' ?>">
                                <i class="bi bi-house-gear me-2"></i> Halaman Utama
                            </a>
                        </li>
                        <li>
                            <a href="/cms/tentang" class="<?= $isCmsTentang ? 'active' : '' ?>">
                                <i class="bi bi-info-square me-2"></i> Tentang Kami
                            </a>
                        </li>
                        <li>
                            <a href="/cms/regulasi" class="<?= $isCmsRegulasi ? 'active' : '' ?>">
                                <i class="bi bi-file-earmark-pdf me-2"></i> Regulasi
                            </a>
                        </li>
                        <li>
                            <a href="/cms/lelang" class="<?= $isCmsLelang ? 'active' : '' ?>">
                                <i class="bi bi-megaphone me-2"></i> Lelang Lainnya
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="/users" class="<?= (strpos(uri_string(), 'users') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-people me-2 text-center" style="width: 20px;"></i> <span>Kelola Pengguna</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <li>
                    <a href="/auth/logout">
                        <i class="bi bi-box-arrow-right me-2 text-center" style="width: 20px;"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 main-content-wrapper">
            <!-- Navbar Atas -->
            <nav class="navbar navbar-expand-lg bg-transparent px-3 px-md-4 py-3 py-md-4 mb-2 top-app-navbar">
                <div class="container-fluid d-flex justify-content-between align-items-center gap-2 flex-nowrap">
                    <div class="d-flex align-items-center gap-2 gap-sm-3 min-w-0">
                        <button type="button" class="btn btn-light border rounded-3 d-lg-none flex-shrink-0 sidebar-toggle-btn" id="mobileSidebarToggle" aria-label="Buka Menu" aria-controls="appSidebar" aria-expanded="false">
                            <i class="bi bi-list fs-4 lh-1"></i>
                        </button>
                        <div class="d-flex flex-column min-w-0">
                            <span class="navbar-brand mb-0 pb-0" style="font-size: 21px; font-weight: 700; color: var(--text-dark); line-height: 1.2;">
                                SIM-INKINDO DKI
                            </span>
                            <small class="text-truncate" style="font-size: 12px; color: var(--text-muted); font-weight: 500;">
                                <?= session()->get('role') === 'mitra'
                                    ? 'Sistem Informasi &amp; Manajemen INKINDO DKI &bull; Portal Mitra Kerja'
                                    : 'Sistem Informasi &amp; Manajemen INKINDO DKI' ?>
                            </small>
                        </div>
                    </div>
                    <a href="<?= esc(rtrim(config('App')->webProfileURL, '/')); ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2 px-sm-3 fw-semibold flex-shrink-0">
                        <i class="bi bi-globe2 me-sm-1"></i> <span class="d-none d-sm-inline">Lihat Web Profile</span><span class="d-inline d-sm-none">Web</span>
                    </a>
                </div>
            </nav>

            <div class="container-fluid px-3 px-sm-4 px-lg-5 pb-4 pb-lg-5">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="container py-3 py-md-5">
            <?= $this->renderSection('content') ?>
        </div>
    <?php endif; ?>

    <!-- jQuery (required for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <style>
        /* Custom Pagination DataTables */
        .dataTables_wrapper .dataTables_paginate .pagination {
            justify-content: flex-end;
            margin-top: 15px;
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item .page-link {
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            padding: 6px 12px;
            margin: 0 2px;
            border-radius: 8px;
            transition: all 0.2s ease;
            box-shadow: none;
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: #f1f5f9;
            color: var(--inkindo-blue);
        }
        .dataTables_wrapper .dataTables_paginate .pagination .page-item.active .page-link {
            background-color: transparent;
            color: var(--inkindo-blue);
            font-weight: 700;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 20px !important;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            padding: 6px 14px;
            border: 1px solid #e2e8f0;
            background-color: var(--white);
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--inkindo-blue);
            box-shadow: 0 0 0 3px rgba(10, 100, 164, 0.1);
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.table-custom').DataTable({
                "pageLength": 6, // Batas maksimal sehalaman
                "lengthChange": false, // Menyembunyikan opsi ubah jumlah baris agar rapi
                "language": {
                    "emptyTable": "Tidak ada data yang tersedia pada tabel ini",
                    "info": "Menampilkan _START_-_END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0-0 dari 0 data",
                    "infoFiltered": "(disaring dari _MAX_ total data)",
                    "lengthMenu": "Tampilkan _MENU_ data",
                    "loadingRecords": "Sedang memuat...",
                    "processing": "Sedang memproses...",
                    "search": "Cari Cepat:",
                    "zeroRecords": "Tidak ditemukan data yang sesuai",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "<i class='bi bi-chevron-right'></i>",
                        "previous": "<i class='bi bi-chevron-left'></i>"
                    }
                },
                "ordering": false // Menonaktifkan sorting bawaan
            });

            var sidebar = document.getElementById('appSidebar');
            var backdrop = document.getElementById('mobileSidebarBackdrop');
            var toggleBtn = document.getElementById('mobileSidebarToggle');
            var closeBtn = document.getElementById('mobileSidebarClose');

            function setSidebarOpen(open) {
                if (!sidebar || !backdrop) return;
                sidebar.classList.toggle('is-open', open);
                backdrop.classList.toggle('show', open);
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                document.body.style.overflow = open && window.innerWidth < 992 ? 'hidden' : '';
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    setSidebarOpen(!sidebar.classList.contains('is-open'));
                });
            }
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    setSidebarOpen(false);
                });
            }
            if (backdrop) {
                backdrop.addEventListener('click', function () {
                    setSidebarOpen(false);
                });
            }
            if (sidebar) {
                sidebar.querySelectorAll('a[href]').forEach(function (link) {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 992 && !link.hasAttribute('data-bs-toggle') && link.getAttribute('href') !== '#') {
                            setSidebarOpen(false);
                        }
                    });
                });
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && sidebar && sidebar.classList.contains('is-open')) {
                    setSidebarOpen(false);
                }
            });
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 992 && sidebar && sidebar.classList.contains('is-open')) {
                    setSidebarOpen(false);
                }
            });
        });
    </script>
</body>
</html>
