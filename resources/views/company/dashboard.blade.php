@extends('layouts.app')
@section('title', $company->name . ' | Dashboard')
@section('page_title', 'Company Dashboard')

@section('content')
    

    <div class="company-dashboard modern-dashboard">
        <section class="modern-hero company-hero">
            <div class="company-logo-box">
                @if ($company->logo_path)
                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }}">
                @else
                    <span
                        class="avatar-title rounded bg-primary-subtle text-primary fs-28 fw-bold">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="modern-hero-main flex-grow-1 min-w-0">
                <span class="modern-kicker">Company Workspace</span>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h3 class="mb-0">{{ $company->name }}</h3><span
                        class="badge text-bg-primary">{{ str($company->type)->replace('_', ' ')->title() }}</span>
                </div>
                <div class="company-hero-meta">
                    @if ($company->company_email)
                        <span><i class="ti ti-mail"></i>{{ $company->company_email }}</span>
                    @endif
                    @if ($company->phone)
                        <span><i class="ti ti-phone"></i>{{ $company->phone }}</span>
                    @endif
                    @if ($company->city)
                        <span><i class="ti ti-map-pin"></i>{{ $company->city }}</span>
                    @endif
                </div>
                <div class="company-hero-strip">
                    <span><i class="ti ti-building-store"></i>{{ $company->code }}</span>
                    <span><i class="ti ti-coins"></i>{{ $company->currency?->code ?? 'Currency' }}</span>
                    <span><i class="ti ti-calendar-stats"></i>{{ now()->format('F Y') }}</span>
                </div>
            </div>
            <div class="modern-hero-actions">
                <a class="btn btn-primary" href="{{ route('company.expenses.create') }}"><i
                        class="ti ti-plus me-1"></i>New Expense</a>
                <a class="btn btn-light" href="{{ route('company.vehicle-invoices.create') }}"><i
                        class="ti ti-file-invoice me-1"></i>New Invoice</a>
                @can('manage-users')
                    <a class="btn btn-light" href="{{ route('company.users.create') }}"><i
                            class="ti ti-user-plus me-1"></i>Add User</a>
                @endcan
            </div>
        </section>

        <ul class="nav nav-tabs nav-bordered mb-3 flex-nowrap overflow-auto company-tabs">
            <li class="nav-item"><a class="nav-link active" href="{{ route('company.dashboard') }}"><i
                        class="ti ti-layout-dashboard me-1"></i>Overview</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('company.expenses.index') }}"><i
                        class="ti ti-receipt me-1"></i>Expenses</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('company.expense-types.index') }}"><i
                        class="ti ti-tags me-1"></i>Types</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('company.roles.index') }}"><i
                        class="ti ti-user-shield me-1"></i>Roles</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('company.reports.expenses') }}"><i
                        class="ti ti-chart-bar me-1"></i>Reports</a></li>
        </ul>

        <div class="modern-stat-grid">
            @foreach ($cards as $card)
                @php
                    $tone =
                        ['primary' => 'primary', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger', 'info' => 'info', 'secondary' => 'secondary'][
                            $card['tone']
                        ] ?? 'primary';
                    $icon =
                        ['people' => 'users', 'tags' => 'tags', 'person-badge' => 'user-shield', 'cash-coin' => 'cash', 'invoice' => 'file-invoice', 'salary' => 'wallet'][
                            $card['icon']
                        ] ?? 'chart-bar';
                @endphp
                <div class="modern-stat">
                    <span class="modern-stat-icon text-{{ $tone }}"><i class="ti ti-{{ $icon }}"></i></span>
                    <span>{{ $card['label'] }}</span>
                    <strong>{{ !empty($card['money']) ? ($company->currency?->symbol ?? 'PKR') . ' ' . number_format($card['value'], 2) : number_format($card['value']) }}</strong>
                </div>
            @endforeach
        </div>

        <div class="modern-panel company-chart-panel">
            <div class="modern-panel-head">
                <div>
                    <h3>Finance Charts</h3>
                    <p>Separate monthly graphs for expenses, invoices and salaries</p>
                </div>
            </div>
            <div class="company-chart-sections">
                @foreach ([
                    'expenses' => ['label' => 'Expenses', 'bar' => 'chart-bar-expenses'],
                    'invoices' => ['label' => 'Invoices', 'bar' => 'chart-bar-invoices'],
                    'salaries' => ['label' => 'Salaries', 'bar' => 'chart-bar-salaries'],
                ] as $metric => $chart)
                    @php
                        $metricMax = max(1, $financeChart->max($metric));
                    @endphp
                    <div class="company-mini-chart">
                        <div class="company-mini-chart-head">
                            <strong><i class="ti ti-chart-bar"></i>{{ $chart['label'] }}</strong>
                            <span>{{ $company->currency?->symbol ?? 'PKR' }} {{ number_format($financeChart->sum($metric), 2) }}</span>
                        </div>
                        <div class="company-mini-chart-body">
                            @foreach ($financeChart as $month)
                                <div class="company-mini-chart-month">
                                    <span class="company-chart-bar {{ $chart['bar'] }}"
                                        style="height: {{ max(4, round(($month[$metric] / $metricMax) * 100)) }}%"
                                        title="{{ $chart['label'] }}: {{ $company->currency?->symbol ?? 'PKR' }} {{ number_format($month[$metric], 2) }}"></span>
                                    <small>{{ $month['label'] }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="modern-panel">
                    <div class="modern-panel-head">
                        <div>
                            <h3>Recent Expenses</h3>
                            <p>Latest entries for {{ $company->name }}</p>
                        </div><a href="{{ route('company.expenses.index') }}" class="btn btn-sm btn-light">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table modern-table align-middle mb-0" data-dx-grid data-dx-page-size="10">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $expense)
                                    <tr>
                                        <td><a class="fw-semibold"
                                                href="{{ route('company.expenses.show', $expense) }}">{{ $expense->invoice_no }}</a>
                                        </td>
                                        <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                        <td>{{ $expense->head?->name ?? '-' }}</td>
                                        <td><span class="badge text-bg-success">{{ str($expense->status)->title() }}</span>
                                        </td>
                                        <td class="text-end fw-semibold">{{ $company->currency?->symbol }}
                                            {{ number_format($expense->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">No expenses have been
                                            recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="modern-panel-foot"><span
                            class="text-muted fw-semibold">This month's expenses</span><strong
                            class="fs-18 text-success">{{ $company->currency?->symbol ?? 'PKR' }}
                            {{ number_format($monthlyExpense, 2) }}</strong></div>
                </div>

                <div class="modern-panel mb-0">
                    <div class="modern-panel-head">
                        <h3>Company Details</h3>
                    </div>
                    <div class="card-body modern-detail-body">
                        <div class="row g-3">
                            <div class="col-sm-6"><span class="text-muted d-block fs-12 text-uppercase">Company
                                    code</span><strong>{{ $company->code }}</strong></div>
                            <div class="col-sm-6"><span
                                    class="text-muted d-block fs-12 text-uppercase">Currency</span><strong>{{ $company->currency?->symbol }}
                                    {{ $company->currency?->code ?? '-' }}</strong></div>
                            <div class="col-sm-6"><span class="text-muted d-block fs-12 text-uppercase">Authorized
                                    person</span><strong>{{ $company->authorized_person ?? '-' }}</strong></div>
                            <div class="col-sm-6"><span
                                    class="text-muted d-block fs-12 text-uppercase">Designation</span><strong>{{ $company->designation ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="modern-panel">
                    <div class="modern-panel-head">
                        <h3>Quick Actions</h3>
                    </div>
                    <div class="modern-list">
                        <a href="{{ route('company.expenses.create') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-circle-plus"></i>New Expense</span><i class="ti ti-chevron-right"></i></a>
                        <a href="{{ route('company.expense-types.index') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-tags"></i>Expense Types</span><i class="ti ti-chevron-right"></i></a>
                        <a href="{{ route('company.roles.index') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-user-shield"></i>Roles</span><i class="ti ti-chevron-right"></i></a>
                        <a href="{{ route('company.permissions.index') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-shield-check"></i>Permissions</span><span class="badge bg-light text-body">{{ $permissionCount }}</span></a>
                        <a href="{{ route('company.reports.expenses') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-chart-bar"></i>Expense Report</span><i class="ti ti-chevron-right"></i></a>
                        <a href="{{ route('company.warranty-providers.index') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-shield-check"></i>Warranty Providers</span><i class="ti ti-chevron-right"></i></a>
                        <a href="{{ route('company.settings.edit') }}"
                            class="modern-list-item modern-link"><span><i class="ti ti-settings-cog"></i>Company Settings</span><i class="ti ti-chevron-right"></i></a>
                    </div>
                </div>

                <div class="modern-panel">
                    <div class="modern-panel-head">
                        <h3>Company Users</h3>
                        @can('manage-users')
                            <a href="{{ route('company.users.index') }}" class="btn btn-sm btn-light">View all</a>
                        @endcan
                    </div>
                    <div class="modern-list">
                        @forelse($recentUsers as $member)
                            <div class="modern-list-item"><span class="d-flex align-items-center gap-2 min-w-0"><span class="avatar-sm"><span
                                        class="avatar-title rounded-circle bg-primary-subtle text-primary fw-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span></span>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="fw-semibold text-truncate">{{ $member->name }}</div><small
                                        class="text-muted text-truncate d-block">{{ $member->email }}</small>
                                </div></span><span
                                    class="badge {{ (int) ($member->company_role_num ?? 0) === 0 ? 'text-bg-success' : 'text-bg-warning' }}">{{ (int) ($member->company_role_num ?? 0) === 0 ? 'Full' : 'R' . $member->company_role_num }}</span>
                            </div>
                        @empty
                            <div class="modern-list-item text-center text-muted py-4">No users assigned.</div>
                        @endforelse
                    </div>
                </div>

                <div class="modern-panel mb-0">
                    <div class="modern-panel-head">
                        <h3>Configuration</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-12 text-uppercase fw-semibold mb-2">Roles</p>
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            @forelse($roles as $role)
                            <span class="badge bg-info-subtle text-info">{{ $role->name }}</span>@empty<span
                                    class="text-muted">No roles</span>
                            @endforelse
                        </div>
                        <p class="text-muted fs-12 text-uppercase fw-semibold mb-2">Expense Types</p>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($expenseTypes as $type)
                            <span class="badge bg-success-subtle text-success">{{ $type->name }}</span>@empty<span
                                    class="text-muted">No expense types</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
