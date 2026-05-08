<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - BusMania')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @stack('styles')
</head>
<body class="admin-body">

    <!-- ══════════════════════════════════════
         ADMIN SIDEBAR
    ══════════════════════════════════════ -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo"><i class="ph-fill ph-bus"></i></div>
            <div>
                <div class="sidebar-brand-name">BusMania</div>
                <div class="sidebar-brand-sub">ADMIN SISTEM</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="ph ph-squares-four"></i> Dashboard
            </a>
            <a href="{{ route('admin.operasional') }}" class="sidebar-link {{ request()->routeIs('admin.operasional*') ? 'active' : '' }}">
                <i class="ph ph-note-pencil"></i> Operasional
            </a>
            <a href="{{ route('admin.transaksi') }}" class="sidebar-link {{ request()->routeIs('admin.transaksi') ? 'active' : '' }}">
                <i class="ph ph-receipt"></i> Transaksi
            </a>
            <a href="{{ route('admin.laporan') }}" class="sidebar-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
                <i class="ph ph-chart-bar"></i> Laporan
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="/" class="sidebar-logout">
                <i class="ph ph-sign-out"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- ══════════════════════════════════════
         ADMIN MAIN AREA
    ══════════════════════════════════════ -->
    <div class="admin-main">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="topbar-search">
                <i class="ph ph-magnifying-glass"></i>
                <input type="text" placeholder="@yield('searchPlaceholder', 'Cari rute, armada, atau tiket...')" class="topbar-search-input">
            </div>
            <div class="topbar-profile">
                <div class="topbar-profile-info">
                    <span class="topbar-name">ini nama admin</span>
                    <span class="topbar-role">Admin</span>
                </div>
                <div class="topbar-avatar"><i class="ph ph-user-circle-fill"></i></div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="admin-content">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
