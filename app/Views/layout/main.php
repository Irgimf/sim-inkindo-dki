<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard INKINDO') ?></title>
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
        }
        .main-content-wrapper {
            background-color: var(--white);
            border-radius: 30px;
            margin: 15px 15px 15px 0;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
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
            background-color: var(--inkindo-blue);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(10, 100, 164, 0.25);
        }
        .table-custom tbody tr:hover td {
            color: var(--white) !important;
        }
        .table-custom tbody tr:hover td .badge {
            background-color: rgba(255,255,255,0.2) !important;
            color: var(--white) !important;
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
    </style>
</head>
<body>

    <?php if(session()->get('logged_in')): ?>
    <div class="d-flex" style="min-height: 100vh;">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column">
            <div class="sidebar-header">
                <img src="/logo-inkindo.png" alt="INKINDO DKI" style="max-width: 65%; height: auto;">
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
                <li>
                    <a href="/cms" class="<?= (strpos(uri_string(), 'cms') === 0) ? 'active' : '' ?>">
                        <i class="bi bi-newspaper me-2 text-center" style="width: 20px;"></i> <span>Admin CMS</span>
                    </a>
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
            <nav class="navbar navbar-expand-lg bg-transparent px-4 py-4 mb-2">
                <div class="container-fluid d-flex justify-content-between align-items-center">
                    <span class="navbar-brand mb-0" style="font-size: 22px; font-weight: 700; color: var(--text-dark);">
                        <?= session()->get('role') === 'mitra' ? 'Sistem Manajemen Mitra Kerja' : 'Sistem Manajemen Data PT' ?>
                    </span>
                    <a href="http://localhost:8000" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                        <i class="bi bi-globe2 me-1"></i> Lihat Web Profile
                    </a>
                </div>
            </nav>

            <div class="container-fluid px-5 pb-5">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="container mt-5">
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
        });
    </script>
</body>
</html>
