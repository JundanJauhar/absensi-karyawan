<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Enterprise Sistem Karyawan')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --primary: #2563eb;
            --sidebar-bg: #1e293b;
            --sidebar-text: #cbd5e1;
            --sidebar-hover: #334155;
        }
        body { background-color: #f1f5f9; font-family: 'Segoe UI', system-ui, sans-serif; }

        /* Sidebar */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--sidebar-bg); z-index: 1040;
            display: flex; flex-direction: column;
            transition: transform .3s ease; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem; border-bottom: 1px solid #334155;
            display: flex; align-items: center; gap: .75rem; text-decoration: none;
        }
        .sidebar-brand .brand-icon {
            width: 38px; height: 38px; background: var(--primary); border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; flex-shrink: 0;
        }
        .sidebar-brand .brand-name { color: #f8fafc; font-size: .95rem; font-weight: 700; }
        .sidebar-brand .brand-sub { color: var(--sidebar-text); font-size: .72rem; }
        .sidebar-nav { padding: 1rem 0; flex: 1; }
        .nav-label {
            padding: .4rem 1.5rem; font-size: .68rem; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase; color: #64748b; margin-top: .5rem;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .75rem; padding: .65rem 1.5rem;
            color: var(--sidebar-text); text-decoration: none; font-size: .875rem;
            border-left: 3px solid transparent; transition: background .2s, color .2s;
        }
        .sidebar-link:hover { background: var(--sidebar-hover); color: #f1f5f9; }
        .sidebar-link.active {
            background: rgba(37,99,235,.15); color: #93c5fd;
            border-left-color: var(--primary); font-weight: 600;
        }
        .sidebar-link i { font-size: 1.05rem; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 1rem 1.5rem; border-top: 1px solid #334155;
            font-size: .8rem; color: #475569; text-align: center;
        }

        /* Topbar */
        #topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem; z-index: 1030;
        }
        .topbar-toggle { background: none; border: none; cursor: pointer; color: #64748b; font-size: 1.3rem; display: none; }
        .topbar-title { font-size: 1rem; font-weight: 600; color: #1e293b; flex: 1; }
        .topbar-actions { display: flex; align-items: center; gap: .75rem; }
        .avatar {
            width: 36px; height: 36px; background: var(--primary); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: .85rem; font-weight: 700; cursor: pointer;
        }

        /* Main */
        #main-content {
            margin-left: var(--sidebar-width); margin-top: var(--topbar-height);
            padding: 1.75rem; min-height: calc(100vh - var(--topbar-height));
        }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 12px; }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; border-radius: 12px 12px 0 0 !important; padding: 1rem 1.25rem; }
        .table th { font-size: .8rem; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #64748b; background: #f8fafc; }
        .table td { vertical-align: middle; font-size: .875rem; }

        /* Responsive */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            #topbar { left: 0; }
            #main-content { margin-left: 0; }
            .topbar-toggle { display: block; }
        }
        #sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 1039;
        }
        #sidebar-overlay.show { display: block; }
    </style>
    @stack('styles')
</head>
<body>
<div id="sidebar-overlay"></div>

<nav id="sidebar">
    <a href="{{ url('/') }}" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-building-fill"></i></div>
        <div>
            <div class="brand-name">Enterprise HR</div>
            <div class="brand-sub">Sistem Manajemen SDM</div>
        </div>
    </a>
    <div class="sidebar-nav">
        @if(auth()->check() && auth()->user()->isKaryawan())
            {{-- ══ MENU KHUSUS KARYAWAN ══ --}}
            <div class="nav-label">Portal Karyawan</div>
            <a href="{{ route('portal.index') }}" class="sidebar-link {{ request()->routeIs('portal.index') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Presensi Mandiri
            </a>
            <a href="{{ route('portal.cuti.index') }}" class="sidebar-link {{ request()->routeIs('portal.cuti.*') ? 'active' : '' }}">
                <i class="bi bi-calendar2-check"></i> Pengajuan Cuti &amp; Izin
            </a>
            <a href="{{ route('portal.face-setup') }}" class="sidebar-link {{ request()->routeIs('portal.face-setup') ? 'active' : '' }}">
                <i class="bi bi-person-bounding-box"></i> Setup Face ID
            </a>
        @else
            {{-- ══ MENU ADMIN HR ══ --}}
            <div class="nav-label">Utama</div>
            <a href="{{ route('karyawan.index') }}" class="sidebar-link {{ request()->is('karyawan') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('karyawan.grafik') }}" class="sidebar-link {{ request()->routeIs('karyawan.grafik') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Grafik Analisis Karyawan
            </a>
            <div class="nav-label">Data SDM</div>
            <a href="{{ url('/struktur') }}" class="sidebar-link {{ request()->is('struktur*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3-fill"></i> Struktur Organisasi
            </a>
            <div class="nav-label">Kehadiran &amp; Perizinan</div>
            <a href="{{ route('absensi.index') }}" class="sidebar-link {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check-fill"></i> Absensi &amp; Approval
            </a>
            <a href="{{ route('cuti.index') }}" class="sidebar-link {{ request()->routeIs('cuti.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-medical"></i> Approval Cuti &amp; Izin
            </a>
            <div class="nav-label">Pengaturan Sistem</div>
            <a href="{{ route('pengaturan.lokasi') }}" class="sidebar-link {{ request()->routeIs('pengaturan.lokasi*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt-fill"></i> Radius Lokasi Kantor
            </a>
            <div class="nav-label">Akses Cepat</div>
            <a href="{{ route('portal.index') }}" class="sidebar-link text-info">
                <i class="bi bi-person-badge"></i> Pratinjau Portal ESS
            </a>
        @endif
    </div>
    <div class="sidebar-footer">&copy; {{ date('Y') }} Enterprise HR System</div>
</nav>

<header id="topbar">
    <button class="topbar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <div class="topbar-title">
        @yield('page-title', 'Dashboard')
        @if(auth()->check())
            <span class="badge {{ auth()->user()->isAdmin() ? 'bg-primary' : 'bg-success' }} ms-2" style="font-size: 0.72rem;">
                {{ strtoupper(auth()->user()->role) }}
            </span>
        @endif
    </div>
    <div class="topbar-actions">
        <div class="dropdown">
            <div class="d-flex align-items-center gap-2 dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;">
                <div class="avatar shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                    <div class="fw-bold small text-dark">{{ auth()->user()->name ?? 'Tamu' }}</div>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ auth()->user()->email ?? '' }}</small>
                </div>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                <li>
                    <h6 class="dropdown-header">
                        {{ auth()->user()->name ?? 'User' }}
                    </h6>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger py-2">
                            <i class="bi bi-box-arrow-right me-2"></i>Keluar (Logout)
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<main id="main-content">
    @hasSection('breadcrumb')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">@yield('breadcrumb')</ol>
    </nav>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebarToggle?.addEventListener('click', () => {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });
    setTimeout(() => {
        document.querySelectorAll('.alert.show').forEach(el => new bootstrap.Alert(el).close());
    }, 5000);
</script>
@stack('scripts')
</body>
</html>
