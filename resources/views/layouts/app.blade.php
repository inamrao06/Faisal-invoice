<!doctype html>
<html lang="en" data-layout-width="fluid" data-layout-position="fixed" data-sidenav-size="default">
@php
    $appSettings = \App\Models\Setting::pluck('value', 'key');
    $siteName = $appSettings['site_name'] ?? $appSettings['business_name'] ?? 'Car Business';
    $themeMode = $appSettings['theme_mode'] ?? 'light';
    $accent = $appSettings['accent_color'] ?? '#4a81d4';
    $sidebarBg = $appSettings['sidebar_color'] ?? '#1f2533';
    $headerBg = $appSettings['header_color'] ?? '#ffffff';
    $user = auth()->user();
    $pageTitle = trim($__env->yieldContent('page_title', 'Dashboard'));
@endphp
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard') | {{ $siteName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $siteName }} management dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('paces/assets/images/favicon.ico') }}">
    <script>
        document.documentElement.setAttribute('data-bs-theme', @json($themeMode));
        document.documentElement.setAttribute('data-menu-color', 'dark');
        document.documentElement.setAttribute('data-topbar-color', 'light');
    </script>
    <script src="{{ asset('paces/assets/js/config.js') }}"></script>
    <link href="{{ asset('paces/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link id="app-style" href="{{ asset('paces/assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/paces-custom.css') }}?v=1" rel="stylesheet">
    <style>:root { --app-accent: {{ $accent }}; --app-sidebar: {{ $sidebarBg }}; --app-topbar: {{ $headerBg }}; }</style>
    @stack('styles')
</head>
<body>
<div class="wrapper">
    <header class="app-topbar">
        <div class="container-fluid topbar-menu">
            <div class="d-flex align-items-center gap-2">
                <div class="logo-topbar">
                    <a href="{{ route('dashboard') }}" class="logo-light"><span class="logo-lg app-wordmark">{{ $siteName }}</span><span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></a>
                    <a href="{{ route('dashboard') }}" class="logo-dark"><span class="logo-lg app-wordmark text-dark">{{ $siteName }}</span><span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></a>
                </div>
                <button class="sidenav-toggle-button btn btn-primary btn-icon" type="button" aria-label="Toggle navigation" title="Toggle navigation"><i class="ti ti-menu-4"></i></button>
                <div class="d-none d-md-block ms-2"><h4 class="mb-0 fs-16 fw-semibold">{{ $pageTitle }}</h4><span class="text-muted fs-12">{{ $user->isSuperUser() ? 'System administration' : ($user->company?->name ?? $siteName) }}</span></div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <div class="topbar-item d-none d-md-flex"><button class="topbar-link" type="button" data-toggle="fullscreen" aria-label="Fullscreen" title="Fullscreen"><i class="ti ti-maximize fs-22"></i></button></div>
                <div class="topbar-item d-none d-sm-flex"><button class="topbar-link" id="light-dark-mode" type="button" aria-label="Toggle color mode" title="Toggle color mode"><i class="ti ti-moon fs-22"></i></button></div>
                <div class="topbar-item"><button class="topbar-link" data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" type="button" aria-label="Display settings" title="Display settings"><i class="ti ti-settings fs-22"></i></button></div>
                <div class="topbar-item nav-user">
                    <div class="dropdown">
                        <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                            <span class="avatar-sm"><span class="avatar-title rounded-circle bg-primary text-white fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span></span>
                            <span class="d-none d-lg-flex flex-column text-start ms-2"><span class="fw-semibold">{{ $user->name }}</span><span class="fs-12 text-muted">{{ $user->accessLabel() }}</span></span>
                            <i class="ti ti-chevron-down d-none d-lg-block ms-1"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="dropdown-header noti-title"><h6 class="text-overflow m-0">Signed in as {{ $user->name }}</h6></div>
                            @can('manage-settings')<a href="{{ route('settings.general') }}" class="dropdown-item"><i class="ti ti-settings-2 me-2"></i>Settings</a>@endcan
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger fw-semibold" type="submit"><i class="ti ti-logout me-2"></i>Log out</button></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="sidenav-menu">
        <a href="{{ route('dashboard') }}" class="logo">
            <span class="logo logo-light"><span class="logo-lg app-wordmark text-white">{{ $siteName }}</span><span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></span>
            <span class="logo logo-dark"><span class="logo-lg app-wordmark text-dark">{{ $siteName }}</span><span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></span>
        </a>
        <button class="button-on-hover" type="button"><span class="btn-on-hover-icon"></span></button>
        <button class="button-close-offcanvas" type="button" aria-label="Close navigation"><i class="ti ti-menu-4 align-middle"></i></button>
        <div class="scrollbar" data-simplebar>
            <div class="sidenav-user app-sidenav-user"><div class="d-flex align-items-center gap-2"><span class="avatar-md"><span class="avatar-title rounded-circle bg-primary text-white fs-20 fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span></span><span class="overflow-hidden"><span class="sidenav-user-name fw-bold d-block text-truncate">{{ $user->name }}</span><span class="fs-12 text-muted d-block text-truncate">{{ $user->accessLabel() }}</span></span></div></div>
            <div id="sidenav-menu"><ul class="side-nav">
                <li class="side-nav-title mt-2">Main</li>
                <li class="side-nav-item"><a href="{{ route('dashboard') }}" class="side-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-dashboard"></i></span><span class="menu-text">Dashboard</span></a></li>
                @if($user->isSuperUser())
                    <li class="side-nav-item"><a href="{{ route('companies.index') }}" class="side-nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-building"></i></span><span class="menu-text">Companies</span></a></li>
                    <li class="side-nav-item"><a href="{{ route('users.index') }}" class="side-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-users"></i></span><span class="menu-text">Users</span></a></li>
                    <li class="side-nav-title mt-2">Administration</li>
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#system-settings" aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}" class="side-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-settings"></i></span><span class="menu-text">Settings</span><span class="menu-arrow"></span></a>
                        <div class="collapse {{ request()->routeIs('settings.*') ? 'show' : '' }}" id="system-settings"><ul class="sub-menu">
                            <li class="side-nav-item"><a href="{{ route('settings.general') }}" class="side-nav-link {{ request()->routeIs('settings.general') ? 'active' : '' }}"><span class="menu-text">General</span></a></li>
                            <li class="side-nav-item"><a href="{{ route('settings.company-types.index') }}" class="side-nav-link {{ request()->routeIs('settings.company-types.*') ? 'active' : '' }}"><span class="menu-text">Company Types</span></a></li>
                            <li class="side-nav-item"><a href="{{ route('settings.currencies.index') }}" class="side-nav-link {{ request()->routeIs('settings.currencies.*') ? 'active' : '' }}"><span class="menu-text">Currencies</span></a></li>
                            <li class="side-nav-item"><a href="{{ route('settings.theme') }}" class="side-nav-link {{ request()->routeIs('settings.theme') ? 'active' : '' }}"><span class="menu-text">Theme</span></a></li>
                            <li class="side-nav-item"><a href="{{ route('settings.notifications.index') }}" class="side-nav-link {{ request()->routeIs('settings.notifications.*') ? 'active' : '' }}"><span class="menu-text">Notifications</span></a></li>
                        </ul></div>
                    </li>
                @else
                    @can('company-area')
                        <li class="side-nav-title mt-2">Company</li>
                        <li class="side-nav-item"><a href="{{ route('company.dashboard') }}" class="side-nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-layout-dashboard"></i></span><span class="menu-text">Overview</span></a></li>
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#expense-menu" aria-expanded="{{ request()->routeIs('company.expense*') || request()->routeIs('company.reports.expenses') ? 'true' : 'false' }}" class="side-nav-link {{ request()->routeIs('company.expense*') || request()->routeIs('company.reports.expenses') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-receipt-2"></i></span><span class="menu-text">Expenses</span><span class="menu-arrow"></span></a>
                            <div class="collapse {{ request()->routeIs('company.expense*') || request()->routeIs('company.reports.expenses') ? 'show' : '' }}" id="expense-menu"><ul class="sub-menu">
                                <li class="side-nav-item"><a href="{{ route('company.expenses.index') }}" class="side-nav-link {{ request()->routeIs('company.expenses.index') || request()->routeIs('company.expenses.show') ? 'active' : '' }}"><span class="menu-text">All Expenses</span></a></li>
                                <li class="side-nav-item"><a href="{{ route('company.expenses.create') }}" class="side-nav-link {{ request()->routeIs('company.expenses.create') ? 'active' : '' }}"><span class="menu-text">New Expense</span></a></li>
                                <li class="side-nav-item"><a href="{{ route('company.expense-types.index') }}" class="side-nav-link {{ request()->routeIs('company.expense-types.*') ? 'active' : '' }}"><span class="menu-text">Expense Types</span></a></li>
                                <li class="side-nav-item"><a href="{{ route('company.reports.expenses') }}" class="side-nav-link {{ request()->routeIs('company.reports.expenses') ? 'active' : '' }}"><span class="menu-text">Reports</span></a></li>
                            </ul></div>
                        </li>
                        @can('manage-users')<li class="side-nav-item"><a href="{{ route('users.index') }}" class="side-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-users"></i></span><span class="menu-text">Users</span></a></li>@endcan
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#access-menu" aria-expanded="{{ request()->routeIs('company.roles.*') || request()->routeIs('company.permissions.*') ? 'true' : 'false' }}" class="side-nav-link {{ request()->routeIs('company.roles.*') || request()->routeIs('company.permissions.*') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-shield-lock"></i></span><span class="menu-text">Access Control</span><span class="menu-arrow"></span></a>
                            <div class="collapse {{ request()->routeIs('company.roles.*') || request()->routeIs('company.permissions.*') ? 'show' : '' }}" id="access-menu"><ul class="sub-menu"><li class="side-nav-item"><a href="{{ route('company.roles.index') }}" class="side-nav-link {{ request()->routeIs('company.roles.*') ? 'active' : '' }}"><span class="menu-text">Roles</span></a></li><li class="side-nav-item"><a href="{{ route('company.permissions.index') }}" class="side-nav-link {{ request()->routeIs('company.permissions.*') ? 'active' : '' }}"><span class="menu-text">Permissions</span></a></li></ul></div>
                        </li>
                        <li class="side-nav-item"><a href="{{ route('company.payment-methods.index') }}" class="side-nav-link {{ request()->routeIs('company.payment-methods.*') ? 'active' : '' }}"><span class="menu-icon"><i class="ti ti-credit-card"></i></span><span class="menu-text">Payment Methods</span></a></li>
                    @endcan
                @endif
            </ul></div>
        </div>
    </div>

    <div class="content-page">
        <div class="container-fluid">
            <div class="page-title-head d-flex align-items-center"><div class="flex-grow-1"><h4 class="page-main-title m-0">{{ $pageTitle }}</h4></div><div class="text-end d-none d-sm-block"><ol class="breadcrumb m-0 py-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $siteName }}</a></li><li class="breadcrumb-item active">{{ $pageTitle }}</li></ol></div></div>
            @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if($errors->any())<div class="alert alert-danger"><i class="ti ti-alert-triangle me-2"></i><strong>Please fix the following:</strong><ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
        <footer class="footer"><div class="container-fluid"><div class="row"><div class="col-md-6 text-center text-md-start">&copy; {{ date('Y') }} {{ $siteName }}</div><div class="col-md-6 text-center text-md-end"><span class="text-muted">{{ $user->company?->name ?? 'Business Management' }}</span></div></div></div></footer>
    </div>
</div>

<div class="offcanvas offcanvas-end overflow-hidden" tabindex="-1" id="theme-settings-offcanvas">
    <div class="d-flex justify-content-between text-bg-primary gap-2 p-3 app-settings-head"><div><h5 class="mb-1 fw-bold text-white">Display settings</h5><p class="text-white text-opacity-75 mb-0 fs-12">Adjust this dashboard on your device.</p></div><button type="button" class="btn btn-sm bg-white bg-opacity-25 text-white rounded-circle btn-icon" data-bs-dismiss="offcanvas"><i class="ti ti-x fs-lg"></i></button></div>
    <div class="offcanvas-body">
        <h6 class="fw-bold mb-3">Color mode</h6><div class="btn-group w-100 mb-4" role="group"><button type="button" class="btn btn-outline-primary app-theme-choice" data-theme="light"><i class="ti ti-sun me-1"></i> Light</button><button type="button" class="btn btn-outline-primary app-theme-choice" data-theme="dark"><i class="ti ti-moon me-1"></i> Dark</button></div>
        <h6 class="fw-bold mb-3">Navigation size</h6><div class="d-grid gap-2"><button type="button" class="btn btn-outline-secondary text-start app-nav-choice" data-size="default"><i class="ti ti-layout-sidebar me-2"></i>Full navigation</button><button type="button" class="btn btn-outline-secondary text-start app-nav-choice" data-size="condensed"><i class="ti ti-layout-sidebar-left-collapse me-2"></i>Compact navigation</button></div>
    </div>
</div>
<script src="{{ asset('paces/assets/js/vendors.min.js') }}"></script>
<script src="{{ asset('paces/assets/js/app.js') }}"></script>
<script>
    document.querySelectorAll('.app-theme-choice').forEach((button) => button.addEventListener('click', () => { const theme = button.dataset.theme; document.documentElement.setAttribute('data-bs-theme', theme); window.config.theme = theme; sessionStorage.setItem('__THEME_CONFIG__', JSON.stringify(window.config)); }));
    document.querySelectorAll('.app-nav-choice').forEach((button) => button.addEventListener('click', () => { const size = button.dataset.size; document.documentElement.setAttribute('data-sidenav-size', size); window.config['sidenav-size'] = size; sessionStorage.setItem('__THEME_CONFIG__', JSON.stringify(window.config)); }));
</script>
@stack('scripts')
</body>
</html>
