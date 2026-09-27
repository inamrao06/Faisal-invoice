<!doctype html>
<html lang="en" data-layout-width="fluid" data-layout-position="fixed" data-sidenav-size="default">
@php
    $appSettings = \App\Models\Setting::pluck('value', 'key');
    $siteName = $appSettings['site_name'] ?? ($appSettings['business_name'] ?? 'Car Business');
    $themeMode = $appSettings['theme_mode'] ?? 'light';
    $accent = $appSettings['accent_color'] ?? '#236dc9';
    $activeColor = $appSettings['active_color'] ?? $accent;
    $borderColor = $appSettings['border_color'] ?? '#e5e7ef';
    $bodyBg = $appSettings['body_bg_color'] ?? ($themeMode === 'dark' ? '#252831' : '#f6f7fb');
    $cardBg = $appSettings['card_bg_color'] ?? ($themeMode === 'dark' ? '#2b2f39' : '#ffffff');
    $textColor = $appSettings['text_color'] ?? ($themeMode === 'dark' ? '#f0f3f6' : '#26313d');
    $mutedColor = $appSettings['muted_color'] ?? ($themeMode === 'dark' ? '#aab3c2' : '#8c98a9');
    $inputBg = $appSettings['input_bg_color'] ?? $cardBg;
    $sidebarBg = $appSettings['sidebar_color'] ?? '#1e1f27';
    $headerBg = $appSettings['header_color'] ?? '#ffffff';
    $safeColor = static fn ($color, $fallback) => preg_match('/^#[0-9a-fA-F]{6}$/', (string) $color) ? $color : $fallback;
    $accent = $safeColor($accent, '#236dc9');
    $activeColor = $safeColor($activeColor, $accent);
    $borderColor = $safeColor($borderColor, '#e5e7ef');
    $bodyBg = $safeColor($bodyBg, $themeMode === 'dark' ? '#252831' : '#f6f7fb');
    $cardBg = $safeColor($cardBg, $themeMode === 'dark' ? '#2b2f39' : '#ffffff');
    $textColor = $safeColor($textColor, $themeMode === 'dark' ? '#f0f3f6' : '#26313d');
    $mutedColor = $safeColor($mutedColor, $themeMode === 'dark' ? '#aab3c2' : '#8c98a9');
    $inputBg = $safeColor($inputBg, $cardBg);
    $sidebarBg = $safeColor($sidebarBg, '#1e1f27');
    $headerBg = $safeColor($headerBg, '#ffffff');
    $logoPath = $appSettings['logo_path'] ?? null;
    $faviconPath = $appSettings['favicon_path'] ?? null;
    $accentRgb = implode(',', array_map('hexdec', str_split(substr($accent, 1), 2)));
    $activeRgb = implode(',', array_map('hexdec', str_split(substr($activeColor, 1), 2)));
    $contrast = static function ($color) {
        [$red, $green, $blue] = array_map('hexdec', str_split(substr($color, 1), 2));
        return ($red * 299 + $green * 587 + $blue * 114) / 1000 < 150 ? '#ffffff' : '#26313d';
    };
    $sidebarText = $contrast($sidebarBg);
    $headerText = $contrast($headerBg);
    $user = auth()->user();
    $pageTitle = trim($__env->yieldContent('page_title', 'Dashboard'));
@endphp

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Dashboard') | {{ $siteName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $siteName }} management dashboard">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon"
        href="{{ $faviconPath ? asset('storage/' . $faviconPath) : asset('paces/assets/images/favicon.ico') }}">
    <script>
        document.documentElement.setAttribute('data-bs-theme', @json($themeMode));
        document.documentElement.setAttribute('data-menu-color', 'dark');
        document.documentElement.setAttribute('data-topbar-color', 'light');
    </script>
    <script src="{{ asset('paces/assets/js/config.js') }}"></script>
    <link href="{{ asset('paces/assets/css/vendors.min.css') }}" rel="stylesheet">
    <link id="app-style" href="{{ asset('paces/assets/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('paces/assets/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dx.light.css') }}" rel="stylesheet">
    <link href="{{ asset('css/paces-custom.css') }}?v=12" rel="stylesheet">
    <style>
        html[data-bs-theme] {
            --app-accent: {{ $accent }};
            --app-active: {{ $activeColor }};
            --app-border: {{ $borderColor }};
            --app-sidebar: {{ $sidebarBg }};
            --app-topbar: {{ $headerBg }};
            --theme-primary: {{ $accent }};
            --theme-primary-rgb: {{ $accentRgb }};
            --theme-active: {{ $activeColor }};
            --theme-active-rgb: {{ $activeRgb }};
            --bs-primary: {{ $accent }};
            --bs-primary-rgb: {{ $accentRgb }};
            --bs-body-bg: {{ $bodyBg }};
            --bs-body-color: {{ $textColor }};
            --bs-secondary-color: {{ $mutedColor }};
            --bs-tertiary-bg: {{ $cardBg }};
            --bs-border-color: {{ $borderColor }};
            --bs-card-bg: {{ $cardBg }};
            --bs-card-border-color: {{ $borderColor }};
            --bs-link-color: {{ $activeColor }};
            --bs-link-hover-color: color-mix(in srgb, {{ $activeColor }} 82%, #000);
            --bs-form-control-bg: {{ $inputBg }};
            --bs-form-control-border-color: {{ $borderColor }};
        }

        html[data-menu-color] {
            --theme-sidenav-bg: {{ $sidebarBg }};
            --theme-sidenav-border-color: {{ $sidebarBg }};
            --theme-sidenav-item-color: color-mix(in srgb, {{ $sidebarText }} 65%, {{ $sidebarBg }});
            --theme-sidenav-item-hover-color: {{ $sidebarText }};
            --theme-sidenav-item-active-color: {{ $sidebarText }};
            --theme-sidenav-item-hover-bg: color-mix(in srgb, {{ $activeColor }} 16%, {{ $sidebarBg }});
            --theme-sidenav-item-active-bg: color-mix(in srgb, {{ $activeColor }} 24%, {{ $sidebarBg }});
        }

        html[data-topbar-color] {
            --theme-topbar-bg: {{ $headerBg }};
            --theme-topbar-item-color: {{ $headerText }};
            --theme-topbar-item-hover-color: {{ $accent }};
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="wrapper">
        <header class="app-topbar">
            <div class="container-fluid topbar-menu">
                <div class="d-flex align-items-center gap-2">
                    <div class="logo-topbar">
                        <a href="{{ route('dashboard') }}" class="logo-light"><span class="logo-lg app-wordmark">
                                @if ($logoPath)
                                    <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $siteName }}">
                                @else
                                    {{ $siteName }}
                                @endif
                            </span>
                            <span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></a>
                        <a href="{{ route('dashboard') }}" class="logo-dark"><span
                                class="logo-lg app-wordmark text-dark">
                                @if ($logoPath)
                                    <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $siteName }}">
                                @else
                                    {{ $siteName }}
                                @endif
                            </span>
                            <span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></a>
                    </div>
                    <button class="sidenav-toggle-button btn btn-primary btn-icon" type="button"
                        aria-label="Toggle navigation" title="Toggle navigation"><i class="ti ti-menu-4"></i></button>
                    <div class="d-none d-md-block ms-2">
                        <h4 class="mb-0 fs-16 fw-semibold">{{ $pageTitle }}</h4><span
                            class="text-muted fs-12">{{ $user->isSuperUser() ? 'System administration' : $user->company?->name ?? $siteName }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <div class="topbar-item d-none d-md-flex"><button class="topbar-link" type="button"
                            data-toggle="fullscreen" aria-label="Fullscreen" title="Fullscreen"><i
                                class="ti ti-maximize fs-22"></i></button></div>
                    <div class="topbar-item d-none d-sm-flex"><button class="topbar-link" id="light-dark-mode"
                            type="button" aria-label="Toggle color mode" title="Toggle color mode"><i
                                class="ti ti-moon fs-22"></i></button></div>
                    <div class="topbar-item"><button class="topbar-link" data-bs-toggle="offcanvas"
                            data-bs-target="#theme-settings-offcanvas" type="button" aria-label="Display settings"
                            title="Display settings"><i class="ti ti-settings fs-22"></i></button></div>
                    <div class="topbar-item nav-user">
                        <div class="dropdown">
                            <a class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-toggle="dropdown"
                                href="#" aria-expanded="false">
                                <span class="avatar-sm"><span
                                        class="avatar-title rounded-circle bg-primary text-white fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span></span>
                                <span class="d-none d-lg-flex flex-column text-start ms-2"><span
                                        class="fw-semibold">{{ $user->name }}</span><span
                                        class="fs-12 text-muted">{{ $user->accessLabel() }}</span></span>
                                <i class="ti ti-chevron-down d-none d-lg-block ms-1"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Signed in as {{ $user->name }}</h6>
                                </div>
                                @can('manage-settings')
                                    <a href="{{ route('settings.general') }}" class="dropdown-item"><i
                                            class="ti ti-settings-2 me-2"></i>Settings</a>
                                @endcan
                                <div class="dropdown-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">@csrf<button
                                        class="dropdown-item text-danger fw-semibold" type="submit"><i
                                            class="ti ti-logout me-2"></i>Log out</button></form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="sidenav-menu">
            <a href="{{ route('dashboard') }}" class="logo">
                <span class="logo logo-light"><span class="logo-lg app-wordmark text-white">
                        @if ($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $siteName }}">
                        @else
                            {{ $siteName }}
                        @endif
                    </span>
                    <span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></span>
                <span class="logo logo-dark"><span class="logo-lg app-wordmark text-dark">
                        @if ($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="{{ $siteName }}">
                        @else
                            {{ $siteName }}
                        @endif
                    </span>
                    <span class="logo-sm app-mark">{{ strtoupper(substr($siteName, 0, 1)) }}</span></span>
            </a>
            <button class="button-on-hover" type="button"><span class="btn-on-hover-icon"></span></button>
            <button class="button-close-offcanvas" type="button" aria-label="Close navigation"><i
                    class="ti ti-menu-4 align-middle"></i></button>
            <div class="scrollbar" data-simplebar>
                <div class="sidenav-user app-sidenav-user">
                    <div class="d-flex align-items-center gap-2"><span class="avatar-md"><span
                                class="avatar-title rounded-circle bg-primary text-white fs-20 fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span></span><span
                            class="overflow-hidden"><span
                                class="sidenav-user-name fw-bold d-block text-truncate">{{ $user->name }}</span><span
                                class="fs-12 text-muted d-block text-truncate">{{ $user->accessLabel() }}</span></span>
                    </div>
                </div>
                <div id="sidenav-menu">
                    <ul class="side-nav">
                        <li class="side-nav-title mt-2">Main</li>
                        <li class="side-nav-item"><a href="{{ route('dashboard') }}"
                                class="side-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><span
                                    class="menu-icon"><i class="ti ti-dashboard"></i></span><span
                                    class="menu-text">Dashboard</span></a></li>
                        @if ($user->isSuperUser())
                            <li class="side-nav-item"><a href="{{ route('companies.index') }}"
                                    class="side-nav-link {{ request()->routeIs('companies.*') ? 'active' : '' }}"><span
                                        class="menu-icon"><i class="ti ti-building"></i></span><span
                                        class="menu-text">Companies</span></a></li>
                            <li class="side-nav-item"><a href="{{ route('users.index') }}"
                                    class="side-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><span
                                        class="menu-icon"><i class="ti ti-users"></i></span><span
                                        class="menu-text">Users</span></a></li>
                            <li class="side-nav-title mt-2">Administration</li>
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#system-settings"
                                    aria-expanded="{{ request()->routeIs('settings.*') ? 'true' : 'false' }}"
                                    class="side-nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}"><span
                                        class="menu-icon"><i class="ti ti-settings"></i></span><span
                                        class="menu-text">Settings</span><span class="menu-arrow"></span></a>
                                <div class="collapse {{ request()->routeIs('settings.*') ? 'show' : '' }}"
                                    id="system-settings">
                                    <ul class="sub-menu">
                                        <li class="side-nav-item"><a href="{{ route('settings.general') }}"
                                                class="side-nav-link {{ request()->routeIs('settings.general') ? 'active' : '' }}"><span
                                                    class="menu-text">General</span></a></li>
                                        <li class="side-nav-item"><a
                                                href="{{ route('settings.company-types.index') }}"
                                                class="side-nav-link {{ request()->routeIs('settings.company-types.*') ? 'active' : '' }}"><span
                                                    class="menu-text">Company Types</span></a></li>
                                        <li class="side-nav-item"><a href="{{ route('settings.currencies.index') }}"
                                                class="side-nav-link {{ request()->routeIs('settings.currencies.*') ? 'active' : '' }}"><span
                                                    class="menu-text">Currencies</span></a></li>
                                        <li class="side-nav-item"><a href="{{ route('settings.theme') }}"
                                                class="side-nav-link {{ request()->routeIs('settings.theme') ? 'active' : '' }}"><span
                                                    class="menu-text">Theme</span></a></li>
                                        <li class="side-nav-item"><a
                                                href="{{ route('settings.notifications.index') }}"
                                                class="side-nav-link {{ request()->routeIs('settings.notifications.*') ? 'active' : '' }}"><span
                                                    class="menu-text">Notifications</span></a></li>
                                    </ul>
                                </div>
                            </li>
                        @else
                            @can('company-area')
                                <li class="side-nav-title mt-2">Company</li>
                                <li class="side-nav-item"><a href="{{ route('company.dashboard') }}"
                                        class="side-nav-link {{ request()->routeIs('company.dashboard') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-layout-dashboard"></i></span><span
                                            class="menu-text">Overview</span></a></li>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#expense-menu"
                                        aria-expanded="{{ request()->routeIs('company.expense*') || request()->routeIs('company.reports.expenses') ? 'true' : 'false' }}"
                                        class="side-nav-link {{ request()->routeIs('company.expense*') || request()->routeIs('company.reports.expenses') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-receipt-2"></i></span><span
                                            class="menu-text">Expenses</span><span class="menu-arrow"></span></a>
                                    <div class="collapse {{ request()->routeIs('company.expense*') || request()->routeIs('company.reports.expenses') ? 'show' : '' }}"
                                        id="expense-menu">
                                        <ul class="sub-menu">
                                            <li class="side-nav-item"><a href="{{ route('company.expenses.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.expenses.index') || request()->routeIs('company.expenses.show') ? 'active' : '' }}"><span
                                                        class="menu-text">All Expenses</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.expenses.create') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.expenses.create') ? 'active' : '' }}"><span
                                                        class="menu-text">New Expense</span></a></li>
                                            <li class="side-nav-item"><a
                                                    href="{{ route('company.expense-types.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.expense-types.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Expense Types</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.reports.expenses') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.reports.expenses') ? 'active' : '' }}"><span
                                                        class="menu-text">Reports</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                @can('manage-users')
                                    <li class="side-nav-item"><a href="{{ route('company.users.index') }}"
                                            class="side-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><span
                                                class="menu-icon"><i class="ti ti-users"></i></span><span
                                                class="menu-text">Users</span></a></li>
                                @endcan
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#sales-menu"
                                        aria-expanded="{{ request()->routeIs('company.vehicle-*') || request()->routeIs('company.customers.*') || request()->routeIs('company.vehicles.*') ? 'true' : 'false' }}"
                                        class="side-nav-link {{ request()->routeIs('company.vehicle-*') || request()->routeIs('company.customers.*') || request()->routeIs('company.vehicles.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-car"></i></span><span
                                            class="menu-text">Vehicle Sales</span><span class="menu-arrow"></span></a>
                                    <div class="collapse {{ request()->routeIs('company.vehicle-*') || request()->routeIs('company.customers.*') || request()->routeIs('company.vehicles.*') ? 'show' : '' }}"
                                        id="sales-menu">
                                        <ul class="sub-menu">
                                            <li class="side-nav-item"><a href="{{ route('company.vehicle-invoices.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.vehicle-invoices.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Invoices</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.customers.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.customers.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Customers</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.vehicles.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.vehicles.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Vehicles</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.vehicle-categories.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.vehicle-categories.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Vehicle Categories</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#car-spec-menu"
                                        aria-expanded="{{ request()->routeIs('company.car-specifications.*') ? 'true' : 'false' }}"
                                        class="side-nav-link {{ request()->routeIs('company.car-specifications.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-file-description"></i></span><span
                                            class="menu-text">Car Specifications</span><span class="menu-arrow"></span></a>
                                    <div class="collapse {{ request()->routeIs('company.car-specifications.*') ? 'show' : '' }}"
                                        id="car-spec-menu">
                                        <ul class="sub-menu">
                                            <li class="side-nav-item"><a href="{{ route('company.vehicle-categories.index') }}" class="side-nav-link"><span class="menu-text">Categories</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.car-specifications.index', 'condition') }}" class="side-nav-link"><span class="menu-text">Conditions</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.car-specifications.index', 'brand') }}" class="side-nav-link"><span class="menu-text">Brands</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.car-specifications.index', 'model') }}" class="side-nav-link"><span class="menu-text">Models</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.car-specifications.index', 'fuel_type') }}" class="side-nav-link"><span class="menu-text">Fuel Types</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.car-specifications.index', 'transmission_type') }}" class="side-nav-link"><span class="menu-text">Transmission Types</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#access-menu"
                                        aria-expanded="{{ request()->routeIs('company.roles.*') || request()->routeIs('company.permissions.*') ? 'true' : 'false' }}"
                                        class="side-nav-link {{ request()->routeIs('company.roles.*') || request()->routeIs('company.permissions.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-shield-lock"></i></span><span
                                            class="menu-text">Access Control</span><span class="menu-arrow"></span></a>
                                    <div class="collapse {{ request()->routeIs('company.roles.*') || request()->routeIs('company.permissions.*') ? 'show' : '' }}"
                                        id="access-menu">
                                        <ul class="sub-menu">
                                            <li class="side-nav-item"><a href="{{ route('company.roles.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.roles.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Roles</span></a></li>
                                            <li class="side-nav-item"><a href="{{ route('company.permissions.index') }}"
                                                    class="side-nav-link {{ request()->routeIs('company.permissions.*') ? 'active' : '' }}"><span
                                                        class="menu-text">Permissions</span></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="side-nav-item"><a href="{{ route('company.payment-methods.index') }}"
                                        class="side-nav-link {{ request()->routeIs('company.payment-methods.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-credit-card"></i></span><span
                                            class="menu-text">Payment Methods</span></a></li>
                                <li class="side-nav-item"><a href="{{ route('company.warranty-providers.index') }}"
                                        class="side-nav-link {{ request()->routeIs('company.warranty-providers.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-shield-check"></i></span><span
                                            class="menu-text">Warranty Providers</span></a></li>
                                <li class="side-nav-item"><a href="{{ route('company.warranty-durations.index') }}"
                                        class="side-nav-link {{ request()->routeIs('company.warranty-durations.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-calendar-time"></i></span><span
                                            class="menu-text">Warranty Durations</span></a></li>
                                <li class="side-nav-item"><a href="{{ route('company.settings.edit') }}"
                                        class="side-nav-link {{ request()->routeIs('company.settings.*') ? 'active' : '' }}"><span
                                            class="menu-icon"><i class="ti ti-settings-cog"></i></span><span
                                            class="menu-text">Settings</span></a></li>
                            @endcan
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="content-page">
            <div class="container-fluid">
                @if (session()->has('impersonation'))
                    <div class="alert alert-warning d-flex align-items-center justify-content-between gap-2 flex-wrap mt-3 mb-2"
                        role="status">
                        <span><i class="ti ti-user-share me-2"></i>Viewing as <strong>{{ $user->name }}</strong>
                            ({{ $user->email }})</span>
                        <form method="POST" action="{{ route('impersonation.stop') }}" class="mb-0">@csrf<button
                                class="btn btn-sm btn-dark" type="submit"><i
                                    class="ti ti-arrow-back-up me-1"></i>Return to Super Admin</button></form>
                    </div>
                @endif
                <div class="page-title-head d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="page-main-title m-0">{{ $pageTitle }}</h4>
                    </div>
                    <div class="text-end d-none d-sm-block">
                        <ol class="breadcrumb m-0 py-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ $siteName }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                        </ol>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert"><i
                            class="ti ti-circle-check me-2"></i>{{ session('success') }}<button type="button"
                            class="btn-close" data-bs-dismiss="alert"></button></div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger"><i class="ti ti-alert-triangle me-2"></i><strong>Please fix the
                            following:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6 text-center text-md-start">&copy; {{ date('Y') }}
                            {{ $siteName }}</div>
                        <div class="col-md-6 text-center text-md-end"><span
                                class="text-muted">{{ $user->company?->name ?? 'Business Management' }}</span></div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <div class="offcanvas offcanvas-end overflow-hidden" tabindex="-1" id="theme-settings-offcanvas">
        <div class="d-flex justify-content-between text-bg-primary gap-2 p-3 app-settings-head">
            <div>
                <h5 class="mb-1 fw-bold text-white">Display settings</h5>
                <p class="text-white text-opacity-75 mb-0 fs-12">Adjust this dashboard on your device.</p>
            </div><button type="button" class="btn btn-sm bg-white bg-opacity-25 text-white rounded-circle btn-icon"
                data-bs-dismiss="offcanvas"><i class="ti ti-x fs-lg"></i></button>
        </div>
        <div class="offcanvas-body">
            <h6 class="fw-bold mb-3">Color mode</h6>
            <div class="btn-group w-100 mb-4" role="group"><button type="button"
                    class="btn btn-outline-primary app-theme-choice" data-theme="light"><i
                        class="ti ti-sun me-1"></i> Light</button><button type="button"
                    class="btn btn-outline-primary app-theme-choice" data-theme="dark"><i
                        class="ti ti-moon me-1"></i> Dark</button></div>
            <h6 class="fw-bold mb-3">Navigation size</h6>
            <div class="d-grid gap-2"><button type="button"
                    class="btn btn-outline-secondary text-start app-nav-choice" data-size="default"><i
                        class="ti ti-layout-sidebar me-2"></i>Full navigation</button><button type="button"
                    class="btn btn-outline-secondary text-start app-nav-choice" data-size="condensed"><i
                        class="ti ti-layout-sidebar-left-collapse me-2"></i>Compact navigation</button></div>
        </div>
    </div>
    <script src="{{ asset('paces/assets/js/vendors.min.js') }}"></script>
    <script src="{{ asset('paces/assets/js/app.js') }}"></script>
    <script src="{{ asset('paces/assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/dx.web.js') }}"></script>
    <script src="{{ asset('paces/assets/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/searchable-selects.js') }}?v=2" defer></script>
    <script>
        document.querySelectorAll('.app-theme-choice').forEach((button) => button.addEventListener('click', () => {
            const theme = button.dataset.theme;
            document.documentElement.setAttribute('data-bs-theme', theme);
            window.config.theme = theme;
            sessionStorage.setItem('__THEME_CONFIG__', JSON.stringify(window.config));
        }));
        document.querySelectorAll('.app-nav-choice').forEach((button) => button.addEventListener('click', () => {
            const size = button.dataset.size;
            document.documentElement.setAttribute('data-sidenav-size', size);
            window.config['sidenav-size'] = size;
            sessionStorage.setItem('__THEME_CONFIG__', JSON.stringify(window.config));
        }));
    </script>
    <script src="{{ asset('js/inner-tables.js') }}?v=1" defer></script>
    <script src="{{ asset('js/devexpress-grids.js') }}?v=3" defer></script>
    <script src="{{ asset('js/devexpress-forms.js') }}?v=1" defer></script>
    @stack('scripts')
</body>

</html>
