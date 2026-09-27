@extends('layouts.app')
@section('title', 'Super Admin Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="modern-dashboard">
    <section class="modern-hero">
        <div class="modern-hero-main">
            <span class="modern-kicker">Super Administrator</span>
            <h2>Welcome back, {{ auth()->user()->name }}</h2>
            <p>Run companies, administrators, currencies, and platform settings from a sharper workspace.</p>
        </div>
        <div class="modern-hero-actions">
            <a class="btn btn-primary" href="{{ route('companies.create') }}"><i class="ti ti-plus me-1"></i>New Company</a>
            <a class="btn btn-light" href="{{ route('users.create') }}"><i class="ti ti-user-plus me-1"></i>New Admin</a>
        </div>
    </section>

    <div class="modern-stat-grid">
        @foreach($cards as $card)
            @php
                $tone = ['primary' => 'primary', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger'][$card['tone']] ?? 'primary';
                $icon = ['buildings' => 'building', 'check2-circle' => 'circle-check', 'person-badge' => 'user-shield', 'graph-up-arrow' => 'chart-line'][$card['icon']] ?? 'chart-bar';
            @endphp
            <div class="modern-stat">
                <span class="modern-stat-icon text-{{ $tone }}"><i class="ti ti-{{ $icon }}"></i></span>
                <span>{{ $card['label'] }}</span>
                <strong>{{ !empty($card['money']) ? number_format($card['value'], 2) : number_format($card['value']) }}</strong>
            </div>
        @endforeach
    </div>

    <div class="modern-action-grid">
        <a href="{{ route('companies.index') }}" class="modern-action"><i class="ti ti-building"></i><span>Companies</span><small>Manage company accounts</small></a>
        <a href="{{ route('users.index') }}" class="modern-action"><i class="ti ti-users"></i><span>Users</span><small>Administrators and access</small></a>
        <a href="{{ route('settings.company-types.index') }}" class="modern-action"><i class="ti ti-tags"></i><span>Company Types</span><small>Configure business modules</small></a>
        <a href="{{ route('settings.currencies.index') }}" class="modern-action"><i class="ti ti-currency-dollar"></i><span>Currencies</span><small>{{ $currencies }} active currencies</small></a>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <div class="modern-panel">
                <div class="modern-panel-head">
                    <div>
                        <h3>Recent Companies</h3>
                        <p>Latest registered company accounts</p>
                    </div>
                    <a href="{{ route('companies.index') }}" class="btn btn-sm btn-light">View all <i class="ti ti-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table modern-table align-middle mb-0" data-dx-grid data-dx-page-size="10">
                        <thead><tr><th>Company</th><th>Type</th><th>Contact</th><th>Currency</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @forelse($recentCompanies as $company)
                            <tr>
                                <td><div class="modern-entity"><span>{{ strtoupper(substr($company->name, 0, 1)) }}</span><div><strong>{{ $company->name }}</strong><small>{{ $company->code }}</small></div></div></td>
                                <td><span class="badge bg-light text-body">{{ str($company->type)->replace('_', ' ')->title() }}</span></td>
                                <td><div class="fw-medium">{{ $company->authorized_person ?: 'Not set' }}</div><small class="text-muted">{{ $company->company_email }}</small></td>
                                <td class="fw-semibold">{{ $company->currency?->code ?? '-' }}</td>
                                <td><span class="badge {{ $company->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="text-end"><a class="btn btn-sm btn-icon btn-light" href="{{ route('companies.show', $company) }}" title="View company"><i class="ti ti-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-5">No companies available.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="modern-panel">
                <div class="modern-panel-head">
                    <h3>Company Types</h3>
                    <a href="{{ route('settings.company-types.index') }}" class="btn btn-sm btn-light">Manage</a>
                </div>
                <div class="modern-list">
                    @forelse($companyTypes as $type)
                        <div class="modern-list-item"><span><i class="ti ti-tag"></i>{{ $type->name }}</span><span class="badge {{ $type->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $type->is_active ? 'Active' : 'Off' }}</span></div>
                    @empty
                        <div class="modern-list-item text-muted">No company types configured.</div>
                    @endforelse
                </div>
            </div>

            <div class="modern-panel mb-0">
                <div class="modern-panel-head"><h3>System Settings</h3></div>
                <div class="modern-list">
                    <a href="{{ route('settings.general') }}" class="modern-list-item modern-link"><span><i class="ti ti-adjustments"></i>General Settings</span><i class="ti ti-chevron-right"></i></a>
                    <a href="{{ route('settings.theme') }}" class="modern-list-item modern-link"><span><i class="ti ti-palette"></i>Theme Settings</span><i class="ti ti-chevron-right"></i></a>
                    <a href="{{ route('settings.notifications.index') }}" class="modern-list-item modern-link"><span><i class="ti ti-bell"></i>Notifications</span><i class="ti ti-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
