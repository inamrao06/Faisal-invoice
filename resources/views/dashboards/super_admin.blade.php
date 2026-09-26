@extends('layouts.app')
@section('title', 'Super Admin Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card mb-0 overflow-hidden">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-lg">
                        <span class="badge text-bg-primary mb-2">Super Administrator</span>
                        <h3 class="mb-2">Welcome back, {{ auth()->user()->name }}</h3>
                        <p class="text-muted mb-0">Manage companies, administrators, currencies, and system configuration from one place.</p>
                    </div>
                    <div class="col-lg-auto">
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-primary" href="{{ route('companies.create') }}"><i class="ti ti-plus me-1"></i>New Company</a>
                            <a class="btn btn-light" href="{{ route('users.create') }}"><i class="ti ti-user-plus me-1"></i>New Admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    @foreach($cards as $card)
        @php
            $tone = ['primary' => 'primary', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger'][$card['tone']] ?? 'primary';
            $icon = ['buildings' => 'building', 'check2-circle' => 'circle-check', 'person-badge' => 'user-shield', 'graph-up-arrow' => 'chart-line'][$card['icon']] ?? 'chart-bar';
        @endphp
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar-lg"><span class="avatar-title rounded bg-{{ $tone }}-subtle text-{{ $tone }}"><i class="ti ti-{{ $icon }} fs-26"></i></span></span>
                        <div class="min-w-0">
                            <p class="text-muted text-uppercase fs-12 fw-semibold mb-1">{{ $card['label'] }}</p>
                            <h3 class="mb-0">{{ !empty($card['money']) ? number_format($card['value'], 2) : number_format($card['value']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-6 col-xl-3"><a href="{{ route('companies.index') }}" class="card mb-0 h-100 text-reset"><div class="card-body"><i class="ti ti-building fs-28 text-primary"></i><h5 class="mt-3 mb-1">Companies</h5><p class="text-muted mb-0">Manage company accounts</p></div></a></div>
    <div class="col-sm-6 col-xl-3"><a href="{{ route('users.index') }}" class="card mb-0 h-100 text-reset"><div class="card-body"><i class="ti ti-users fs-28 text-info"></i><h5 class="mt-3 mb-1">Users</h5><p class="text-muted mb-0">Administrators and access</p></div></a></div>
    <div class="col-sm-6 col-xl-3"><a href="{{ route('settings.company-types.index') }}" class="card mb-0 h-100 text-reset"><div class="card-body"><i class="ti ti-tags fs-28 text-success"></i><h5 class="mt-3 mb-1">Company Types</h5><p class="text-muted mb-0">Configure business modules</p></div></a></div>
    <div class="col-sm-6 col-xl-3"><a href="{{ route('settings.currencies.index') }}" class="card mb-0 h-100 text-reset"><div class="card-body"><i class="ti ti-currency-dollar fs-28 text-warning"></i><h5 class="mt-3 mb-1">Currencies</h5><p class="text-muted mb-0">{{ $currencies }} active currencies</p></div></a></div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="card mb-0">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div><h4 class="card-title mb-1">Recent Companies</h4><p class="text-muted mb-0 fs-12">Latest registered company accounts</p></div>
                <a href="{{ route('companies.index') }}" class="btn btn-sm btn-light">View all <i class="ti ti-arrow-right ms-1"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Company</th><th>Type</th><th>Contact</th><th>Currency</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    @forelse($recentCompanies as $company)
                        <tr>
                            <td><div class="d-flex align-items-center gap-2"><span class="avatar-sm"><span class="avatar-title rounded bg-primary-subtle text-primary fw-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span></span><div><div class="fw-semibold">{{ $company->name }}</div><small class="text-muted">{{ $company->code }}</small></div></div></td>
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
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between"><h4 class="card-title mb-0">Company Types</h4><a href="{{ route('settings.company-types.index') }}" class="btn btn-sm btn-light">Manage</a></div>
            <div class="list-group list-group-flush">
                @forelse($companyTypes as $type)
                    <div class="list-group-item d-flex align-items-center justify-content-between"><span><i class="ti ti-tag text-primary me-2"></i>{{ $type->name }}</span><span class="badge {{ $type->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $type->is_active ? 'Active' : 'Off' }}</span></div>
                @empty
                    <div class="list-group-item text-muted">No company types configured.</div>
                @endforelse
            </div>
        </div>

        <div class="card mb-0">
            <div class="card-header"><h4 class="card-title mb-0">System Settings</h4></div>
            <div class="list-group list-group-flush">
                <a href="{{ route('settings.general') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-adjustments fs-20 text-primary me-3"></i><span>General Settings</span><i class="ti ti-chevron-right ms-auto"></i></a>
                <a href="{{ route('settings.theme') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-palette fs-20 text-info me-3"></i><span>Theme Settings</span><i class="ti ti-chevron-right ms-auto"></i></a>
                <a href="{{ route('settings.notifications.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-bell fs-20 text-warning me-3"></i><span>Notifications</span><i class="ti ti-chevron-right ms-auto"></i></a>
            </div>
        </div>
    </div>
</div>
@endsection
