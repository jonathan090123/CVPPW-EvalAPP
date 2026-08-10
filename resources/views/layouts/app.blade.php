<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PWP - Penilaian Karyawan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --app-primary: #1e3a5f;
            --app-primary-2: #234d75;
            --app-accent: #3b82f6;
            --app-surface: #ffffff;
            --app-muted: #6b7280;
            --app-border: #e5e7eb;
            --app-bg: #f5f7fb;
        }

        body {
            background: linear-gradient(180deg, #f7f9fc 0%, var(--app-bg) 100%);
            color: #1f2937;
            transition: padding-left .3s ease-in-out;
            font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* ===== Topbar (semua device) ===== */
        .topbar {
            background: linear-gradient(90deg, var(--app-primary) 0%, var(--app-primary-2) 100%);
            color: #fff;
            padding: .7rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            display: flex;
            align-items: center;
            gap: .75rem;
            box-shadow: 0 2px 16px rgba(30, 58, 95, 0.18);
        }
        .topbar .btn-toggle-sidebar {
            color: #fff;
            border: none;
            background: transparent;
            font-size: 1.4rem;
            line-height: 1;
            padding: .25rem .5rem;
            cursor: pointer;
            border-radius: .5rem;
        }
        .topbar .btn-toggle-sidebar:hover {
            color: rgba(255,255,255,.9);
            background-color: rgba(255,255,255,.1);
        }
        .topbar .topbar-brand { color: #fff; font-weight: 700; font-size: 1rem; margin: 0; }
        .topbar .topbar-brand i { color: rgba(255,255,255,.8); }
        .topbar .topbar-user-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .45rem .7rem;
            border-radius: 999px;
            background-color: rgba(255,255,255,.15);
            color: #fff;
            font-size: .8rem;
            white-space: nowrap;
        }
        .topbar .topbar-user-pill .topbar-username {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            background: linear-gradient(180deg, var(--app-primary) 0%, #17324f 100%);
            width: 260px;
            max-width: 85vw;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1045;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform .3s ease-in-out, visibility .3s ease-in-out;
            display: flex;
            flex-direction: column;
            padding: 1rem;
            box-shadow: 10px 0 24px rgba(15, 23, 42, 0.18);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            border-radius: .7rem;
            font-weight: 500;
            transition: all .2s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,.14);
            transform: translateX(2px);
        }
        .sidebar .brand { color: #fff; font-size: 1rem; font-weight: 700; line-height: 1.2; }
        .sidebar .nav-section {
            color: rgba(255,255,255,.45);
            font-size: .73rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            padding: .5rem 1rem;
        }
        .btn-close-sidebar {
            color: rgba(255,255,255,.8);
            border: none;
            background: transparent;
            font-size: 1.1rem;
            line-height: 1;
            padding: .25rem .4rem;
            cursor: pointer;
            border-radius: .45rem;
        }
        .btn-close-sidebar:hover { color: #fff; background-color: rgba(255,255,255,.1); }

        /* ===== State terbuka / tertutup ===== */
        .sidebar.open { transform: translateX(0); visibility: visible; }
        .sidebar.closed { transform: translateX(-100%); visibility: hidden; }

        /* Desktop: konten bergeser ke kanan mengikuti sidebar */
        @media (min-width: 768px) {
            body.sidebar-open { padding-left: 260px; }
            body.sidebar-closed { padding-left: 0; }
            .sidebar-backdrop { display: none !important; }
        }
        /* Mobile: sidebar overlay, konten tidak berpindah */
        @media (max-width: 767.98px) {
            body.sidebar-open, body.sidebar-closed { padding-left: 0; }
            .sidebar-backdrop { display: block !important; }
        }

        /* Backdrop (mobile only) */
        .sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity .3s ease-in-out, visibility .3s ease-in-out;
        }
        .sidebar-backdrop.show { opacity: 1; visibility: visible; }

        .main-content {
            min-height: calc(100vh - 49px);
            padding: .75rem .75rem 2rem;
        }
        .content-shell {
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: .75rem;
            margin-bottom: 1rem;
        }
        .page-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: .2rem;
        }
        .page-subtitle {
            color: var(--app-muted);
            font-size: .92rem;
            margin: 0;
        }
        .card {
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #eef2f7;
            font-weight: 700;
        }
        .btn {
            border-radius: .75rem;
            font-weight: 600;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .btn-sm {
            padding: .42rem .72rem;
        }
        .table {
            font-size: .95rem;
        }
        .table thead th {
            background-color: var(--app-primary);
            color: #fff;
            font-size: .78rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .badge-benefit { background-color: #198754; }
        .badge-cost { background-color: #dc3545; }

        @media (max-width: 767.98px) {
            .topbar {
                padding: .65rem .85rem;
            }
            .topbar .topbar-brand {
                font-size: .95rem;
            }
            .topbar .topbar-user-pill .topbar-username {
                max-width: 90px;
            }
            .main-content {
                padding: .8rem .8rem 1.3rem;
            }
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }
            .page-header .btn {
                width: 100%;
            }
            .table-responsive {
                border: 0;
            }
        }
    </style>
</head>
<body class="sidebar-open">

{{-- Topbar (visible on all devices) --}}
<div class="topbar">
    <button class="btn-toggle-sidebar" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
        <i class="bi bi-list" id="sidebarToggleIcon"></i>
    </button>
    <span class="topbar-brand"><i class="bi bi-bar-chart-steps me-1"></i>PWP Penilaian Karyawan</span>
    <div class="ms-auto d-flex align-items-center gap-2 flex-wrap justify-content-end">
        <span class="topbar-user-pill">
            <i class="bi bi-person-circle"></i>
            <span class="topbar-username">{{ auth()->user()?->username ?? 'Guest' }}</span>
        </span>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-light" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>

{{-- Sidebar --}}
<nav class="sidebar open" id="sidebar" aria-label="Main navigation">
    <div class="d-flex align-items-center justify-content-between mb-4 ps-2">
        <span class="brand"><i class="bi bi-bar-chart-steps me-2"></i>PWP PENILAIAN KARYAWAN</span>
        <button class="btn-close-sidebar" type="button" id="sidebarClose" aria-label="Tutup sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <ul class="nav flex-column gap-1">
        <li class="nav-section">Dashboard</li>
        <li class="nav-item">
            <a href="{{ route('assessments.index') }}"
               class="nav-link px-2 py-2 {{ request()->routeIs('assessments.*', 'ranking.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-data me-2"></i>Penilaian
            </a>
        </li>
        <li class="nav-section mt-3">Master Data</li>
        <li class="nav-item">
            <a href="{{ route('employees.index') }}"
               class="nav-link px-2 py-2 {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-people me-2"></i>Karyawan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('criteria.index') }}"
               class="nav-link px-2 py-2 {{ request()->routeIs('criteria.*') ? 'active' : '' }}">
                <i class="bi bi-sliders me-2"></i>Kriteria
            </a>
        </li>
        @if (auth()->user() && auth()->user()->isOwner())
        <li class="nav-item">
            <a href="{{ route('assessments.ownerOverview') }}"
               class="nav-link px-2 py-2 {{ request()->routeIs('assessments.ownerOverview') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-data me-2"></i>Ringkasan Penilaian
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('proportions.edit') }}"
               class="nav-link px-2 py-2 {{ request()->routeIs('proportions.*') ? 'active' : '' }}">
                <i class="bi bi-percent me-2"></i>Setting Proporsi
            </a>
        </li>
        @endif
        <li class="nav-section mt-3">Akun</li>
        <li class="nav-item">
            <a href="{{ route('settings.edit') }}"
               class="nav-link px-2 py-2 {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear me-2"></i>Pengaturan
            </a>
        </li>
    </ul>
</nav>

{{-- Backdrop (mobile only) --}}
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

{{-- Main content --}}
<main class="main-content">
    <div class="content-shell">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Palet warna jabatan (samakan dengan AppServiceProvider::POSITION_PALETTE)
    window.POSITION_PALETTE = {{ \Illuminate\Support\Js::from(\App\Providers\AppServiceProvider::POSITION_PALETTE) }};
    window.POSITION_LIGHT_INDICES = {{ \Illuminate\Support\Js::from(\App\Providers\AppServiceProvider::LIGHT_TEXT_INDICES) }};
    window.positionColor = function (pos) {
        pos = String(pos || '').toLowerCase().trim();
        if (!pos) return '#6c757d';
        var idx = 0;
        for (var i = 0; i < pos.length; i++) { idx = (idx * 31 + pos.charCodeAt(i)) >>> 0; }
        idx = idx % window.POSITION_PALETTE.length;
        return window.POSITION_PALETTE[idx];
    };
    window.positionTextColor = function (pos) {
        pos = String(pos || '').toLowerCase().trim();
        var idx = 0;
        for (var i = 0; i < pos.length; i++) { idx = (idx * 31 + pos.charCodeAt(i)) >>> 0; }
        idx = idx % window.POSITION_PALETTE.length;
        return window.POSITION_LIGHT_INDICES.indexOf(idx) !== -1 ? '#212529' : '#fff';
    };
    window.positionBadgeHtml = function (pos) {
        if (!pos) return '';
        var bg = window.positionColor(pos);
        var fg = window.positionTextColor(pos);
        var escaped = String(pos).replace(/[&<>"']/g, function (c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
        });
        return '<span class="badge rounded-pill ms-1" style="background-color:' + bg + ';color:' + fg + '">' + escaped + '</span>';
    };
</script>
<script>
    (function () {
        var sidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        var toggle = document.getElementById('sidebarToggle');
        var toggleIcon = document.getElementById('sidebarToggleIcon');
        var closeBtn = document.getElementById('sidebarClose');
        var body = document.body;
        var STORAGE_KEY = 'pwp_sidebar_state';
        var isMobile = function () { return window.matchMedia('(max-width: 767.98px)').matches; };

        function applyOpen() {
            sidebar.classList.add('open');
            sidebar.classList.remove('closed');
            body.classList.add('sidebar-open');
            body.classList.remove('sidebar-closed');
            if (isMobile()) backdrop.classList.add('show');
            if (toggleIcon) toggleIcon.className = 'bi bi-list';
        }
        function applyClosed() {
            sidebar.classList.remove('open');
            sidebar.classList.add('closed');
            body.classList.remove('sidebar-open');
            body.classList.add('sidebar-closed');
            backdrop.classList.remove('show');
            if (toggleIcon) toggleIcon.className = 'bi bi-list';
        }
        function setState(open) {
            open ? applyOpen() : applyClosed();
            try { localStorage.setItem(STORAGE_KEY, open ? 'open' : 'closed'); } catch (e) {}
        }
        function isOpen() { return sidebar.classList.contains('open'); }

        // Init: di desktop ikut localStorage (default open); di mobile default closed
        var saved = null;
        try { saved = localStorage.getItem(STORAGE_KEY); } catch (e) {}
        if (isMobile()) {
            setState(false);
        } else {
            setState(saved !== 'closed');
        }

        if (toggle) toggle.addEventListener('click', function () { setState(!isOpen()); });
        if (closeBtn) closeBtn.addEventListener('click', function () { setState(false); });
        if (backdrop) backdrop.addEventListener('click', function () { setState(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setState(false); });

        // Auto-close on mobile when a nav link is clicked (before navigation)
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobile()) setState(false);
            });
        });

        // Saat resize desktop <-> mobile, reset state
        window.addEventListener('resize', function () {
            if (isMobile()) {
                if (isOpen()) setState(false);
            } else {
                var s = null;
                try { s = localStorage.getItem(STORAGE_KEY); } catch (e) {}
                setState(s !== 'closed');
            }
        });
    })();
</script>
@stack('scripts')
</body>
</html>