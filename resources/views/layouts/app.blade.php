<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — RME</title>

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&display=swap" rel="stylesheet">

    {{-- Font Awesome 6.5 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* ── Base ── */
        * { font-family: 'Inter', sans-serif; }
        :root {
            --primary:       #7B1D1D;
            --primary-dark:  #5C1414;
            --primary-light: #9B2C2C;
            --gold:          #D4A017;
            --bg:            #F9F5F5;
            --surface:       #FFFFFF;
            --text:          #1A0A0A;
            --text-muted:    #6B4C4C;
            --border:        #E8D5D5;
            --success:       #276749;
            --warning:       #B7791F;
        }
        body { background: var(--bg); color: var(--text); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #D4A017; border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: #B7791F; }

        /* ── Sidebar ── */
        #sidebar {
            background: linear-gradient(180deg, #5C1414 0%, #7B1D1D 50%, #9B2C2C 100%);
            width: 260px;
            transition: width 0.25s ease, transform 0.25s ease;
            flex-shrink: 0;
        }
        #sidebar.collapsed { width: 64px; }
        #sidebar.collapsed .nav-label,
        #sidebar.collapsed .sidebar-section-label,
        #sidebar.collapsed .user-info,
        #sidebar.collapsed .app-tagline { display: none; }
        #sidebar.collapsed .sidebar-link { justify-content: center; padding-left: 0; padding-right: 0; }
        #sidebar.collapsed .sidebar-link .nav-icon { margin: 0; }
        #sidebar.collapsed .sidebar-toggle-icon { transform: rotate(180deg); }

        /* ── Sidebar links ── */
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            color: rgba(255,255,255,0.75);
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .sidebar-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-weight: 600;
            border-left-color: #D4A017;
        }
        .sidebar-link:not(.active):hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }
        .sidebar-section-label {
            color: rgba(255,255,255,0.4);
            font-size: 0.625rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.875rem 1rem 0.25rem;
        }

        /* ── Mobile sidebar ── */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed;
                z-index: 50;
                transform: translateX(-100%);
                width: 260px !important;
            }
            #sidebar.mobile-open { transform: translateX(0); }
        }

        /* ── Animations ── */
        .sidebar-item { transition: all 0.2s ease; }

        .fade-in { animation: fadeIn 0.3s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.3s ease forwards; }

        .counter-animate { transition: all 0.5s ease; }

        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(197,48,48,0.4); }
            70%  { box-shadow: 0 0 0 8px rgba(197,48,48,0); }
            100% { box-shadow: 0 0 0 0 rgba(197,48,48,0); }
        }
        .pulse-called { animation: pulse-ring 1.5s infinite; }
    </style>

    @stack('head')
</head>
<body class="min-h-screen flex">

    {{-- ═══════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════ --}}
    <aside id="sidebar" class="flex flex-col min-h-screen relative">

        {{-- Logo area --}}
        <div class="px-4 py-5 flex items-center gap-3" style="border-bottom: 1px solid rgba(212,160,23,0.25);">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: rgba(212,160,23,0.2); border: 1px solid rgba(212,160,23,0.4);">
                <i class="fa-solid fa-hospital text-white text-xl"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-sm font-bold text-white leading-tight truncate">{{ config('app.name') }}</h1>
                <p class="app-tagline text-xs mt-0.5" style="color: rgba(212,160,23,0.85);">Rekam Medis Elektronik</p>
            </div>
        </div>

        {{-- Gold divider --}}
        <div style="height: 1px; background: linear-gradient(90deg, rgba(212,160,23,0.6) 0%, rgba(212,160,23,0.1) 100%); margin: 0 1rem;"></div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-2 px-2 space-y-0.5">

            {{-- ── PELAYANAN ── --}}
            <p class="sidebar-section-label">Pelayanan</p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Dashboard</span>
            </a>

            @if(in_array('*', $userPermissions) || in_array('registration', $userPermissions))
            <a href="{{ route('registration.index') }}"
               class="sidebar-link {{ request()->routeIs('registration.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-plus nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Pendaftaran</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('admisi', $userPermissions))
            <a href="{{ route('admisi.index') }}"
               class="sidebar-link {{ request()->routeIs('admisi.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bed-pulse nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Admisi</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('queue', $userPermissions))
            <a href="{{ route('queue.index') }}"
               class="sidebar-link {{ request()->routeIs('queue.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-ol nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Antrian</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('rme', $userPermissions))
            <a href="{{ route('rme.index') }}"
               class="sidebar-link {{ request()->routeIs('rme.*') ? 'active' : '' }}">
                <i class="fa-solid fa-stethoscope nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Rawat Jalan</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('inpatient', $userPermissions))
            <a href="{{ route('inpatient.index') }}"
               class="sidebar-link {{ request()->routeIs('inpatient.*') ? 'active' : '' }}">
                <i class="fa-solid fa-hospital-user nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Rawat Inap</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('lab', $userPermissions))
            <a href="{{ route('lab.index') }}"
               class="sidebar-link {{ request()->routeIs('lab.*') ? 'active' : '' }}">
                <i class="fa-solid fa-flask nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Laboratorium</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('radiology', $userPermissions))
            <a href="{{ route('radiology.index') }}"
               class="sidebar-link {{ request()->routeIs('radiology.*') ? 'active' : '' }}">
                <i class="fa-solid fa-x-ray nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Radiologi</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('pharmacy', $userPermissions))
            <a href="{{ route('pharmacy.index') }}"
               class="sidebar-link {{ request()->routeIs('pharmacy.*') ? 'active' : '' }}">
                <i class="fa-solid fa-pills nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Farmasi</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('billing', $userPermissions))
            <a href="{{ route('billing.index') }}"
               class="sidebar-link {{ request()->routeIs('billing.*') && !request()->routeIs('claims.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Billing</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('claims', $userPermissions))
            <a href="{{ route('berkas-digital.index') }}"
               class="sidebar-link {{ request()->routeIs('berkas-digital.*') || request()->routeIs('claims.*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Berkas Digital</span>
            </a>
            @endif

            {{-- ── MANAJEMEN ── --}}
            @if(in_array('*', $userPermissions) || in_array('report', $userPermissions) || in_array('master.data', $userPermissions))
            <p class="sidebar-section-label">Manajemen</p>
            @endif

            @if(in_array('*', $userPermissions) || in_array('report', $userPermissions))
            <a href="{{ route('report.visits') }}"
               class="sidebar-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Laporan</span>
            </a>
            @endif

            @if(in_array('*', $userPermissions) || in_array('master.data', $userPermissions))
            <a href="{{ route('master.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('master.*') ? 'active' : '' }}">
                <i class="fa-solid fa-database nav-icon w-4 text-center flex-shrink-0"></i>
                <span class="nav-label">Master Data</span>
            </a>
            @endif

        </nav>

        {{-- User info --}}
        <div class="px-3 py-3" style="border-top: 1px solid rgba(212,160,23,0.3);">
            <div class="user-info flex items-center gap-2.5 mb-2.5">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0"
                     style="background: #D4A017; color: #5C1414;">
                    {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->username }}</p>
                    <p class="text-xs capitalize" style="color: rgba(212,160,23,0.85);">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs transition-all"
                        style="color: rgba(255,150,150,0.85); border: 1px solid rgba(255,100,100,0.2);"
                        onmouseover="this.style.background='rgba(255,100,100,0.1)'"
                        onmouseout="this.style.background='transparent'">
                    <i class="fa-solid fa-right-from-bracket nav-icon w-4 text-center flex-shrink-0"></i>
                    <span class="nav-label">Keluar</span>
                </button>
            </form>
        </div>

        {{-- Collapse toggle --}}
        <button id="sidebar-toggle"
                class="absolute -right-3 top-20 w-6 h-6 rounded-full shadow-md flex items-center justify-center text-xs z-10"
                style="background: #D4A017; color: #5C1414; border: 2px solid #fff;"
                title="Toggle sidebar">
            <i class="fa-solid fa-chevron-left sidebar-toggle-icon" style="transition: transform 0.25s ease;"></i>
        </button>
    </aside>

    {{-- Mobile overlay --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 hidden"
         style="background: rgba(0,0,0,0.5);"
         onclick="closeMobileSidebar()"></div>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT AREA
    ═══════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col min-w-0" style="background: #F9F5F5;">

        {{-- Top Header (sticky) --}}
        <header class="sticky top-0 z-30 flex items-center justify-between gap-4 px-5 py-3"
                style="background: #FFFFFF; border-bottom: 1px solid #E8D5D5; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

            <div class="flex items-center gap-3">
                {{-- Mobile hamburger --}}
                <button class="md:hidden p-1.5 rounded-lg"
                        style="color: #7B1D1D;"
                        onclick="openMobileSidebar()">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>

                {{-- Breadcrumb --}}
                <nav class="flex items-center text-sm gap-1.5 min-w-0" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-1 transition-opacity hover:opacity-70"
                       style="color: #6B4C4C;">
                        <i class="fa-solid fa-house text-xs"></i>
                        <span class="hidden sm:inline">Beranda</span>
                    </a>
                    @hasSection('breadcrumb')
                        <span style="color: #E8D5D5;">/</span>
                        @yield('breadcrumb')
                    @endif
                </nav>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-2 flex-shrink-0">

                {{-- API mode badge --}}
                @if($apiMode === 'testing')
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                      style="background: #FFFBEB; color: #B7791F; border: 1px solid #F6E05E;">
                    <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background: #B7791F;"></span>
                    Mode Testing
                </span>
                @elseif($apiMode === 'production')
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                      style="background: #F0FFF4; color: #276749; border: 1px solid #9AE6B4;">
                    <span class="w-1.5 h-1.5 rounded-full" style="background: #276749;"></span>
                    Produksi
                </span>
                @endif

                {{-- Notification bell --}}
                <div class="relative" id="notif-wrapper">
                    <button id="notif-btn"
                            class="relative p-2 rounded-xl transition-colors"
                            style="color: #6B4C4C;"
                            onmouseover="this.style.background='#F9F5F5'"
                            onmouseout="this.style.background='transparent'"
                            aria-label="Notifikasi">
                        <i class="fa-solid fa-bell text-sm"></i>
                        <span id="notif-badge"
                              class="absolute -top-0.5 -right-0.5 w-4 h-4 text-white text-xs rounded-full items-center justify-center hidden font-medium"
                              style="background: #7B1D1D; display: none;">0</span>
                    </button>
                    <div id="notif-dropdown"
                         class="hidden absolute right-0 mt-2 w-80 rounded-xl overflow-hidden z-50"
                         style="background: #FFFFFF; box-shadow: 0 8px 24px rgba(123,29,29,0.15); border: 1px solid #E8D5D5;">
                        <div class="px-4 py-3 flex items-center justify-between"
                             style="background: #F9F5F5; border-bottom: 1px solid #E8D5D5;">
                            <span class="text-sm font-semibold" style="color: #1A0A0A;">Notifikasi</span>
                            <button id="notif-clear" class="text-xs font-medium" style="color: #7B1D1D;">Hapus semua</button>
                        </div>
                        <ul id="notif-list" class="max-h-72 overflow-y-auto divide-y" style="border-color: #E8D5D5;">
                            <li class="px-4 py-6 text-sm text-center" id="notif-empty" style="color: #6B4C4C;">
                                <i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>
                                Tidak ada notifikasi
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- User avatar --}}
                <div class="flex items-center gap-2 pl-2" style="border-left: 1px solid #E8D5D5;">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                         style="background: linear-gradient(135deg, #7B1D1D, #9B2C2C);">
                        {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) }}
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold leading-tight" style="color: #1A0A0A;">{{ auth()->user()->username }}</p>
                        <p class="text-xs capitalize" style="color: #6B4C4C;">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                </div>

            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-auto" style="padding: 1.5rem;">
            @yield('content')
        </main>

    </div>

    <script>
    // ── Sidebar collapse ──
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    let collapsed = false;

    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            collapsed = !collapsed;
            sidebar.classList.toggle('collapsed', collapsed);
            const icon = toggleBtn.querySelector('.sidebar-toggle-icon');
            if (icon) icon.style.transform = collapsed ? 'rotate(180deg)' : 'rotate(0deg)';
        });
    }

    function openMobileSidebar() {
        sidebar.classList.add('mobile-open');
        document.getElementById('sidebar-overlay').classList.remove('hidden');
    }
    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        document.getElementById('sidebar-overlay').classList.add('hidden');
    }

    // ── Notification system ──
    (function () {
        const btn      = document.getElementById('notif-btn');
        const dropdown = document.getElementById('notif-dropdown');
        const badge    = document.getElementById('notif-badge');
        const list     = document.getElementById('notif-list');
        const clearBtn = document.getElementById('notif-clear');
        let notifications = [];

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', function () {
            dropdown.classList.add('hidden');
        });
        clearBtn.addEventListener('click', function () {
            notifications = [];
            renderNotifications();
        });

        function renderNotifications() {
            if (notifications.length === 0) {
                list.innerHTML = '<li class="px-4 py-6 text-sm text-center" style="color:#6B4C4C;"><i class="fa-regular fa-bell-slash text-2xl mb-2 block"></i>Tidak ada notifikasi</li>';
                badge.style.display = 'none';
                return;
            }
            badge.textContent = notifications.length > 9 ? '9+' : notifications.length;
            badge.style.display = 'flex';
            list.innerHTML = notifications.map(function (n) {
                var dotColor = n.type === 'danger' ? '#C53030' : '#7B1D1D';
                return '<li class="px-4 py-3 flex gap-3 items-start" style="cursor:default;" onmouseover="this.style.background=\'#FFF9F9\'" onmouseout="this.style.background=\'transparent\'">' +
                    '<span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0" style="background:' + dotColor + '"></span>' +
                    '<div class="min-w-0"><p class="text-sm leading-snug" style="color:#1A0A0A;">' + escapeHtml(n.message) + '</p>' +
                    '<p class="text-xs mt-0.5" style="color:#6B4C4C;">' + escapeHtml(n.time) + '</p></div></li>';
            }).join('');
        }

        function escapeHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str));
            return d.innerHTML;
        }

        function addNotification(message, type) {
            var now  = new Date();
            var time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            notifications.unshift({ message: message, type: type || 'info', time: time });
            if (notifications.length > 20) notifications.pop();
            renderNotifications();
        }

        window.RMENotify = { add: addNotification };

        if (typeof window.Echo !== 'undefined') {
            var userId = {{ auth()->id() ?? 'null' }};
            if (userId) {
                window.Echo.private('App.Models.User.' + userId)
                    .notification(function (notification) {
                        if (notification.type && notification.type.includes('LabResult')) {
                            addNotification('Hasil lab siap: ' + (notification.message || ''), 'info');
                        }
                        if (notification.type && notification.type.includes('RadiologyResult')) {
                            addNotification('Hasil radiologi siap: ' + (notification.message || ''), 'info');
                        }
                    });
            }
            window.Echo.channel('pharmacy.alerts')
                .listen('.stock.low', function (e) {
                    addNotification('Stok menipis: ' + (e.drug_name || 'obat'), 'danger');
                });
        }
    })();
    </script>

    @stack('scripts')
</body>
</html>
