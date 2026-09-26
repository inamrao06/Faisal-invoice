@extends('layouts.app')
@section('title', $company->name . ' | Dashboard')
@section('page_title', 'Company Dashboard')

@section('content')
<div class="card mb-3">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center gap-3">
            <span class="avatar-xl flex-shrink-0">
                @if($company->logo_path)
                    <img src="{{ asset('storage/'.$company->logo_path) }}" class="rounded" alt="{{ $company->name }}">
                @else
                    <span class="avatar-title rounded bg-primary-subtle text-primary fs-28 fw-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                @endif
            </span>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1"><h3 class="mb-0">{{ $company->name }}</h3><span class="badge text-bg-primary">{{ str($company->type)->replace('_', ' ')->title() }}</span></div>
                <div class="d-flex flex-wrap gap-3 text-muted">
                    @if($company->company_email)<span><i class="ti ti-mail me-1"></i>{{ $company->company_email }}</span>@endif
                    @if($company->phone)<span><i class="ti ti-phone me-1"></i>{{ $company->phone }}</span>@endif
                    @if($company->city)<span><i class="ti ti-map-pin me-1"></i>{{ $company->city }}</span>@endif
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-primary" href="{{ route('company.expenses.create') }}"><i class="ti ti-plus me-1"></i>New Expense</a>
                @can('manage-users')<a class="btn btn-light" href="{{ route('users.create') }}"><i class="ti ti-user-plus me-1"></i>Add User</a>@endcan
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs nav-bordered mb-3 flex-nowrap overflow-auto">
    <li class="nav-item"><a class="nav-link active" href="{{ route('company.dashboard') }}"><i class="ti ti-layout-dashboard me-1"></i>Overview</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('company.expenses.index') }}"><i class="ti ti-receipt me-1"></i>Expenses</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('company.expense-types.index') }}"><i class="ti ti-tags me-1"></i>Types</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('company.roles.index') }}"><i class="ti ti-user-shield me-1"></i>Roles</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('company.reports.expenses') }}"><i class="ti ti-chart-bar me-1"></i>Reports</a></li>
</ul>

<div class="row g-3 mb-3">
    @foreach($cards as $card)
        @php
            $tone = ['primary' => 'primary', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger'][$card['tone']] ?? 'primary';
            $icon = ['people' => 'users', 'tags' => 'tags', 'person-badge' => 'user-shield', 'cash-coin' => 'cash'][$card['icon']] ?? 'chart-bar';
        @endphp
        <div class="col-xl-3 col-sm-6">
            <div class="card mb-0 h-100"><div class="card-body"><div class="d-flex align-items-center gap-3"><span class="avatar-lg"><span class="avatar-title rounded bg-{{ $tone }}-subtle text-{{ $tone }}"><i class="ti ti-{{ $icon }} fs-26"></i></span></span><div><p class="text-muted text-uppercase fs-12 fw-semibold mb-1">{{ $card['label'] }}</p><h3 class="mb-0">{{ !empty($card['money']) ? number_format($card['value'], 2) : number_format($card['value']) }}</h3></div></div></div></div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between"><div><h4 class="card-title mb-1">Recent Expenses</h4><p class="text-muted fs-12 mb-0">Latest entries for {{ $company->name }}</p></div><a href="{{ route('company.expenses.index') }}" class="btn btn-sm btn-light">View all</a></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>Invoice</th><th>Date</th><th>Type</th><th>Status</th><th class="text-end">Amount</th></tr></thead>
                    <tbody>
                    @forelse($recentExpenses as $expense)
                        <tr><td><a class="fw-semibold" href="{{ route('company.expenses.show', $expense) }}">{{ $expense->invoice_no }}</a></td><td>{{ $expense->expense_date->format('d M Y') }}</td><td>{{ $expense->head?->name ?? '-' }}</td><td><span class="badge text-bg-success">{{ str($expense->status)->title() }}</span></td><td class="text-end fw-semibold">{{ $company->currency?->symbol }} {{ number_format($expense->total_amount, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No expenses have been recorded.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center"><span class="text-muted fw-semibold">This month's expenses</span><strong class="fs-18 text-success">{{ $company->currency?->symbol ?? 'PKR' }} {{ number_format($monthlyExpense, 2) }}</strong></div>
        </div>

        <div class="card mb-0">
            <div class="card-header"><h4 class="card-title mb-0">Company Details</h4></div>
            <div class="card-body"><div class="row g-3">
                <div class="col-sm-6"><span class="text-muted d-block fs-12 text-uppercase">Company code</span><strong>{{ $company->code }}</strong></div>
                <div class="col-sm-6"><span class="text-muted d-block fs-12 text-uppercase">Currency</span><strong>{{ $company->currency?->symbol }} {{ $company->currency?->code ?? '-' }}</strong></div>
                <div class="col-sm-6"><span class="text-muted d-block fs-12 text-uppercase">Authorized person</span><strong>{{ $company->authorized_person ?? '-' }}</strong></div>
                <div class="col-sm-6"><span class="text-muted d-block fs-12 text-uppercase">Designation</span><strong>{{ $company->designation ?? '-' }}</strong></div>
            </div></div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header"><h4 class="card-title mb-0">Quick Actions</h4></div>
            <div class="list-group list-group-flush">
                <a href="{{ route('company.expenses.create') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-circle-plus fs-20 text-primary me-3"></i>New Expense<i class="ti ti-chevron-right ms-auto"></i></a>
                <a href="{{ route('company.expense-types.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-tags fs-20 text-success me-3"></i>Expense Types<i class="ti ti-chevron-right ms-auto"></i></a>
                <a href="{{ route('company.roles.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-user-shield fs-20 text-info me-3"></i>Roles<i class="ti ti-chevron-right ms-auto"></i></a>
                <a href="{{ route('company.permissions.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-shield-check fs-20 text-warning me-3"></i>Permissions<span class="badge bg-light text-body ms-auto">{{ $permissionCount }}</span></a>
                <a href="{{ route('company.reports.expenses') }}" class="list-group-item list-group-item-action d-flex align-items-center"><i class="ti ti-chart-bar fs-20 text-danger me-3"></i>Expense Report<i class="ti ti-chevron-right ms-auto"></i></a>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between"><h4 class="card-title mb-0">Company Users</h4>@can('manage-users')<a href="{{ route('users.index') }}" class="btn btn-sm btn-light">View all</a>@endcan</div>
            <div class="list-group list-group-flush">
                @forelse($recentUsers as $member)
                    <div class="list-group-item d-flex align-items-center gap-2"><span class="avatar-sm"><span class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span></span><div class="min-w-0 flex-grow-1"><div class="fw-semibold text-truncate">{{ $member->name }}</div><small class="text-muted text-truncate d-block">{{ $member->email }}</small></div><span class="badge {{ (int)($member->company_role_num ?? 0) === 0 ? 'text-bg-success' : 'text-bg-warning' }}">{{ (int)($member->company_role_num ?? 0) === 0 ? 'Full' : 'R'.$member->company_role_num }}</span></div>
                @empty
                    <div class="list-group-item text-center text-muted py-4">No users assigned.</div>
                @endforelse
            </div>
        </div>

        <div class="card mb-0">
            <div class="card-header"><h4 class="card-title mb-0">Configuration</h4></div>
            <div class="card-body">
                <p class="text-muted fs-12 text-uppercase fw-semibold mb-2">Roles</p><div class="d-flex flex-wrap gap-1 mb-3">@forelse($roles as $role)<span class="badge bg-info-subtle text-info">{{ $role->name }}</span>@empty<span class="text-muted">No roles</span>@endforelse</div>
                <p class="text-muted fs-12 text-uppercase fw-semibold mb-2">Expense Types</p><div class="d-flex flex-wrap gap-1">@forelse($expenseTypes as $type)<span class="badge bg-success-subtle text-success">{{ $type->name }}</span>@empty<span class="text-muted">No expense types</span>@endforelse</div>
            </div>
        </div>
    </div>
</div>
@endsection
