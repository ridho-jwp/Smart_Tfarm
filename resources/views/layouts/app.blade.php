<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Pakcoy Hidroponik')</title>
    <link rel="icon" type="image/png" href="{{ asset('wowdash/images/favicon.png') }}" sizes="16x16">
    <!-- remix icon font css -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/remixicon.css') }}">
    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/lib/bootstrap.min.css') }}">
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/lib/apexcharts.css') }}">
    <!-- Data Table css -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/lib/dataTables.min.css') }}">
    <!-- Date picker css -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/lib/flatpickr.min.css') }}">
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('wowdash/css/style.css') }}">
    @stack('styles')
</head>

<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn">
            <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
        </button>
        <div>
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <span class="fw-bold fs-5 ms-2" style="font-family:'Poppins',sans-serif;color:var(--primary-color,#45a049);">Smart Pakcoy</span>
            </a>
        </div>
        <div class="sidebar-menu-area">
            <ul class="sidebar-menu" id="sidebar-menu">
                <li class="sidebar-menu-group-title">Monitoring</li>
                <li class="{{ request()->routeIs('dashboard') ? 'active-page' : '' }}">
                    <a href="{{ route('dashboard') }}">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="menu-icon"></iconify-icon>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ request()->routeIs('history') ? 'active-page' : '' }}">
                    <a href="{{ route('history') }}">
                        <iconify-icon icon="solar:history-outline" class="menu-icon"></iconify-icon>
                        <span>Riwayat Sensor</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('anomalies') ? 'active-page' : '' }}">
                    <a href="{{ route('anomalies') }}">
                        <iconify-icon icon="solar:camera-outline" class="menu-icon"></iconify-icon>
                        <span>Deteksi Hama Daun</span>
                    </a>
                </li>

                <li class="sidebar-menu-group-title">Manajemen</li>
                
                <li class="{{ request()->routeIs('configs.index') ? 'active-page' : '' }}">
                    <a href="{{ route('configs.index') }}">
                        <iconify-icon icon="icon-data" class="menu-icon"></iconify-icon>
                        <span>Konfigurasi Data</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('') ? 'active-page' : '' }}">
                    <a href="{{""}}">
                        <iconify-icon icon="icon-park-outline:setting-two" class="menu-icon"></iconify-icon>
                        <span>Setup Perangkat</span>
                    </a>
                </li>
                
                <li class="mt-4">
                    <a href="{{ route('logout') }}">
                        <iconify-icon icon="logout" class="menu-icon"></iconify-icon>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-main">

        <!-- Navbar -->
        <div class="navbar-header">
            <div class="row align-items-center justify-content-between">
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <button type="button" class="sidebar-toggle">
                            <iconify-icon icon="heroicons:bars-3-solid" class="icon text-2xl non-active"></iconify-icon>
                            <iconify-icon icon="iconoir:arrow-right" class="icon text-2xl active"></iconify-icon>
                        </button>
                        <button type="button" class="sidebar-mobile-toggle">
                            <iconify-icon icon="heroicons:bars-3-solid" class="icon"></iconify-icon>
                        </button>
                        <span class="text-secondary-light fw-medium d-none d-md-block">
                            <iconify-icon icon="solar:clock-circle-outline" class="me-1"></iconify-icon>
                            <span id="current-time"></span>
                        </span>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <button type="button" data-theme-toggle
                            class="w-40-px h-40-px bg-neutral-200 rounded-circle d-flex justify-content-center align-items-center"></button>

                        <!-- User dropdown -->
                        <div class="dropdown">
                            <button class="d-flex justify-content-center align-items-center rounded-circle"
                                type="button" data-bs-toggle="dropdown">
                                <div
                                    class="w-40-px h-40-px bg-primary-600 rounded-circle d-flex justify-content-center align-items-center text-white fw-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            </button>
                            <div class="dropdown-menu to-top dropdown-menu-sm">
                                <div
                                    class="py-12 px-16 radius-8 bg-primary-50 mb-16 d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <h6 class="text-lg text-primary-light fw-semibold mb-2">
                                            {{ auth()->user()->name }}</h6>
                                        <span
                                            class="text-secondary-light fw-medium text-sm">{{ ucfirst(auth()->user()->role ?? 'User') }}</span>
                                    </div>
                                </div>
                                <ul class="to-top-list">
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="dropdown-item text-black px-0 py-8 hover-bg-transparent hover-text-danger d-flex align-items-center gap-3 w-100 border-0 bg-transparent">
                                                <iconify-icon icon="lucide:power" class="icon text-xl"></iconify-icon>
                                                Keluar
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="dashboard-main-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-24"
                    role="alert">
                    <iconify-icon icon="bitcoin-icons:verify-outline" class="text-success-main text-xl"></iconify-icon>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-24"
                    role="alert">
                    <iconify-icon icon="solar:close-circle-outline" class="text-danger-main text-xl"></iconify-icon>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>

    </main>

    <!-- jQuery library js -->
    <script src="{{ asset('wowdash/js/lib/jquery-3.7.1.min.js') }}"></script>
    <!-- Bootstrap js -->
    <script src="{{ asset('wowdash/js/lib/bootstrap.bundle.min.js') }}"></script>
    <!-- Apex Chart js -->
    <script src="{{ asset('wowdash/js/lib/apexcharts.min.js') }}"></script>
    <!-- Iconify Font js -->
    <script src="{{ asset('wowdash/js/lib/iconify-icon.min.js') }}"></script>
    <!-- Data Table js -->
    <script src="{{ asset('wowdash/js/lib/dataTables.min.js') }}"></script>
    <!-- main js -->
    <script src="{{ asset('wowdash/js/app.js') }}"></script>

    <script>
        // Update current time in navbar
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        updateTime();
        setInterval(updateTime, 60000);
    </script>

    @stack('scripts')
</body>

</html>