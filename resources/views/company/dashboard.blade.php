@extends('layouts.app')
@section('title', $company->name . ' — Dashboard')
@section('page_title', 'Company Dashboard')
@section('content')
<style>
/* ── Company Hero ─────────────────────────────────────────── */
.cd-hero{display:flex;align-items:center;gap:22px;background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#2563eb 100%);color:#fff;border-radius:16px;padding:26px 30px;margin-bottom:22px;box-shadow:0 12px 40px rgba(37,99,235,.2);position:relative;overflow:hidden;flex-wrap:wrap}
.cd-hero::after{content:'';position:absolute;right:-40px;top:-40px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.06);pointer-events:none}
.cd-logo{width:80px;height:80px;border-radius:14px;background:#fff;display:grid;place-items:center;font-size:30px;font-weight:900;color:#2563eb;overflow:hidden;flex:0 0 auto;box-shadow:0 8px 24px rgba(0,0,0,.2)}
.cd-logo img{width:100%;height:100%;object-fit:contain;padding:8px}
.cd-body{flex:1;min-width:0}
.cd-type{display:inline-block;font-size:11px;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;font-weight:700;margin-bottom:6px}
.cd-body h2{font-size:26px;font-weight:900;margin:0 0 8px}
.cd-meta{display:flex;flex-wrap:wrap;gap:14px;font-size:13px;color:#bfdbfe}
.cd-meta span{display:flex;align-items:center;gap:5px}
.cd-actions{display:flex;gap:10px;flex-wrap:wrap;flex:0 0 auto}

/* ── Tabs ─────────────────────────────────────────────────── */
.cd-tabs{display:flex;gap:6px;flex-wrap:wrap;background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:6px;margin-bottom:22px;box-shadow:0 2px 8px rgba(15,23,42,.04)}
.cd-tabs a{padding:9px 16px;border-radius:8px;color:#475569!important;font-weight:700;font-size:13px;transition:.15s}
.cd-tabs a.active,.cd-tabs a:hover{background:#dbeafe;color:#1d4ed8!important}

/* ── Metrics ──────────────────────────────────────────────── */
.cd-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px}
.cd-metric{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;display:flex;align-items:center;gap:14px;box-shadow:0 4px 16px rgba(15,23,42,.05);transition:.2s}
.cd-metric:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(15,23,42,.1)}
.cd-metric-icon{width:50px;height:50px;border-radius:12px;display:grid;place-items:center;font-size:21px;flex:0 0 auto}
.cd-metric-icon.blue{background:#dbeafe;color:#2563eb}
.cd-metric-icon.green{background:#dcfce7;color:#16a34a}
.cd-metric-icon.amber{background:#fef3c7;color:#d97706}
.cd-metric-icon.rose{background:#fee2e2;color:#dc2626}
.cd-metric-body small{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:3px}
.cd-metric-body strong{font-size:24px;font-weight:900;color:#0f172a;line-height:1}

/* ── Grid layout ──────────────────────────────────────────── */
.cd-grid{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:18px}
.cd-panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 4px 16px rgba(15,23,42,.05);overflow:hidden;margin-bottom:18px}
.cd-panel:last-child{margin-bottom:0}
.cd-panel-head{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #f1f5f9}
.cd-panel-head h3{margin:0;font-size:14px;font-weight:800;color:#0f172a}
.cd-panel-head p{margin:2px 0 0;font-size:12px;color:#94a3b8}
.cd-panel-head a{font-size:13px;font-weight:700;color:#2563eb}
.cd-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;background:#fafbfc;padding:10px 18px;border-bottom:1px solid #f1f5f9}
.cd-table tbody td{padding:12px 18px;font-size:13px;border-bottom:1px solid #f1f5f9;color:#1e293b}
.cd-table tbody tr:last-child td{border-bottom:0}
.cd-table tbody tr:hover td{background:#fafbff}

/* ── Info grid ────────────────────────────────────────────── */
.cd-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:0}
.cd-info-cell{padding:13px 20px;border-right:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9}
.cd-info-cell:nth-child(2n){border-right:0}
.cd-info-cell:nth-last-child(-n+2){border-bottom:0}
.cd-info-cell small{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:3px}
.cd-info-cell strong{font-size:14px;font-weight:700;color:#1e293b}

/* ── Quick Actions ────────────────────────────────────────── */
.qa-list{display:grid;gap:8px;padding:14px}
.qa-item{display:flex;align-items:center;gap:12px;padding:11px 13px;border-radius:9px;background:#f8fafc;border:1px solid #e2e8f0;color:#1e293b!important;font-weight:700;font-size:13px;transition:.15s}
.qa-item:hover{background:#eff6ff;border-color:#93c5fd;color:#2563eb!important}
.qa-item-icon{width:32px;height:32px;border-radius:7px;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-size:14px;flex:0 0 auto}

/* ── Users mini ───────────────────────────────────────────── */
.mini-user{display:flex;align-items:center;gap:10px;padding:11px 20px;border-bottom:1px solid #f1f5f9}
.mini-user:last-child{border-bottom:0}
.mini-av{width:36px;height:36px;border-radius:50%;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-weight:900;font-size:14px;flex:0 0 auto}
.mini-name{font-weight:700;font-size:13px;color:#1e293b;line-height:1.3}
.mini-email{font-size:11px;color:#94a3b8}
.mini-badge{padding:3px 8px;border-radius:999px;font-size:10px;font-weight:800}
.mini-badge.full{background:#dcfce7;color:#15803d}
.mini-badge.role{background:#fef3c7;color:#92400e}

/* ── Tag list ─────────────────────────────────────────────── */
.tag-cloud{display:flex;gap:7px;flex-wrap:wrap;padding:14px 18px}
.tag-cloud span{display:inline-flex;padding:6px 12px;border-radius:999px;background:#ede9fe;color:#6d28d9;font-weight:700;font-size:12px}

/* ── Monthly stat ─────────────────────────────────────────── */
.monthly-stat{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:linear-gradient(90deg,#f0fdf4,#dcfce7);border-top:1px solid #f1f5f9}
.monthly-stat small{font-size:12px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.06em}
.monthly-stat strong{font-size:20px;font-weight:900;color:#14532d}

@media(max-width:1100px){.cd-grid{grid-template-columns:1fr}}
@media(max-width:700px){.cd-hero{flex-direction:column;align-items:flex-start}.cd-metrics{grid-template-columns:1fr 1fr}}
@media(max-width:480px){.cd-metrics{grid-template-columns:1fr}}
</style>

{{-- HERO --}}
<div class="cd-hero">
    <div class="cd-logo">
        @if($company->logo_path)
            <img src="{{ asset('storage/'.$company->logo_path) }}" alt="{{ $company->name }}">
        @else
            {{ strtoupper(substr($company->name,0,1)) }}
        @endif
    </div>
    <div class="cd-body">
        <div class="cd-type">{{ str($company->type)->replace('_',' ')->title() }}</div>
        <h2>{{ $company->name }}</h2>
        <div class="cd-meta">
            @if($company->company_email)
                <span><i class="bi bi-envelope-fill"></i>{{ $company->company_email }}</span>
            @endif
            @if($company->phone)
                <span><i class="bi bi-telephone-fill"></i>{{ $company->phone }}</span>
            @endif
            @if($company->city)
                <span><i class="bi bi-geo-alt-fill"></i>{{ $company->city }}</span>
            @endif
            @if($company->tax_number)
                <span><i class="bi bi-receipt"></i>{{ $company->tax_number }}</span>
            @endif
        </div>
    </div>
    <div class="cd-actions">
        <a class="btn btn-light fw-bold" href="{{ route('company.expenses.create') }}">
            <i class="bi bi-plus-lg"></i> New Expense
        </a>
        @can('manage-users')
            <a class="btn btn-outline-light" href="{{ route('users.create') }}">
                <i class="bi bi-person-plus"></i> Add User
            </a>
        @endcan
    </div>
</div>

{{-- TABS --}}
<div class="cd-tabs">
    <a class="active" href="{{ route('company.dashboard') }}"><i class="bi bi-grid me-1"></i>Overview</a>
    <a href="{{ route('company.expenses.index') }}"><i class="bi bi-cash-coin me-1"></i>Expenses</a>
    <a href="{{ route('company.expense-types.index') }}"><i class="bi bi-tags me-1"></i>Expense Types</a>
    <a href="{{ route('company.roles.index') }}"><i class="bi bi-person-badge me-1"></i>Roles</a>
    <a href="{{ route('company.permissions.index') }}"><i class="bi bi-shield-check me-1"></i>Permissions</a>
    <a href="{{ route('company.reports.expenses') }}"><i class="bi bi-bar-chart me-1"></i>Reports</a>
</div>

{{-- METRICS --}}
<div class="cd-metrics">
    @foreach($cards as $card)
        <div class="cd-metric">
            <div class="cd-metric-icon {{ $card['tone'] === 'primary' ? 'blue' : ($card['tone'] === 'success' ? 'green' : ($card['tone'] === 'warning' ? 'amber' : 'rose')) }}">
                <i class="bi bi-{{ $card['icon'] }}"></i>
            </div>
            <div class="cd-metric-body">
                <small>{{ $card['label'] }}</small>
                <strong>{{ !empty($card['money']) ? number_format($card['value'],2) : number_format($card['value']) }}</strong>
            </div>
        </div>
    @endforeach
</div>

{{-- MAIN GRID --}}
<div class="cd-grid">

    {{-- LEFT --}}
    <div>
        {{-- Company Info --}}
        <div class="cd-panel">
            <div class="cd-panel-head">
                <div><h3>Company Details</h3><p>Profile &amp; configuration</p></div>
                <a href="{{ route('companies.show',$company) }}">View full <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="cd-info-grid">
                <div class="cd-info-cell"><small>Code</small><strong>{{ $company->code }}</strong></div>
                <div class="cd-info-cell"><small>Currency</small><strong>{{ $company->currency?->symbol }} {{ $company->currency?->code ?? '—' }}</strong></div>
                <div class="cd-info-cell"><small>Authorized Person</small><strong>{{ $company->authorized_person ?? '—' }}</strong></div>
                <div class="cd-info-cell"><small>Designation</small><strong>{{ $company->designation ?? '—' }}</strong></div>
            </div>
            <div class="monthly-stat">
                <small><i class="bi bi-calendar-check me-1"></i>This month's expenses</small>
                <strong>{{ $company->currency?->symbol ?? 'PKR' }} {{ number_format($monthlyExpense,2) }}</strong>
            </div>
        </div>

        {{-- Recent Expenses --}}
        <div class="cd-panel">
            <div class="cd-panel-head">
                <div><h3>Recent Expenses</h3><p>Latest expense entries</p></div>
                <a href="{{ route('company.expenses.index') }}">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table cd-table mb-0">
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
                                <td>
                                    <a href="{{ route('company.expenses.show',$expense) }}"
                                       style="font-weight:700;color:#2563eb">
                                        {{ $expense->invoice_no }}
                                    </a>
                                </td>
                                <td>{{ $expense->expense_date->format('d M Y') }}</td>
                                <td>{{ $expense->head?->name ?? '—' }}</td>
                                <td>
                                    <span class="status on">{{ str($expense->status)->title() }}</span>
                                </td>
                                <td class="text-end" style="font-weight:700">
                                    {{ $company->currency?->symbol ?? '' }} {{ number_format($expense->total_amount,2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding:32px;text-align:center;color:#94a3b8">
                                    No expenses yet.
                                    <a href="{{ route('company.expenses.create') }}">Create one →</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT --}}
    <div>

        {{-- Quick Actions --}}
        <div class="cd-panel">
            <div class="cd-panel-head">
                <div><h3>Quick Actions</h3><p>Common tasks</p></div>
            </div>
            <div class="qa-list">
                <a class="qa-item" href="{{ route('company.expenses.create') }}">
                    <span class="qa-item-icon"><i class="bi bi-plus-circle"></i></span>
                    New Expense
                </a>
                <a class="qa-item" href="{{ route('company.expense-types.index') }}">
                    <span class="qa-item-icon" style="background:#ccfbf1;color:#0d9488"><i class="bi bi-tags"></i></span>
                    Expense Types
                </a>
                <a class="qa-item" href="{{ route('company.roles.index') }}">
                    <span class="qa-item-icon" style="background:#ede9fe;color:#7c3aed"><i class="bi bi-person-badge"></i></span>
                    Roles
                </a>
                <a class="qa-item" href="{{ route('company.permissions.index') }}">
                    <span class="qa-item-icon" style="background:#fef3c7;color:#d97706"><i class="bi bi-shield-check"></i></span>
                    Permissions
                    <span style="margin-left:auto;font-size:11px;color:#94a3b8">{{ $permissionCount }} rules</span>
                </a>
                <a class="qa-item" href="{{ route('company.reports.expenses') }}">
                    <span class="qa-item-icon" style="background:#fee2e2;color:#dc2626"><i class="bi bi-bar-chart"></i></span>
                    Expense Report
                </a>
            </div>
        </div>

        {{-- Company Users --}}
        <div class="cd-panel">
            <div class="cd-panel-head">
                <div><h3>Company Users</h3><p>Recently added</p></div>
                @can('manage-users')
                    <a href="{{ route('users.index') }}">View all</a>
                @endcan
            </div>
            @forelse($recentUsers as $user)
                <div class="mini-user">
                    <div class="mini-av">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <div style="flex:1;min-width:0">
                        <div class="mini-name">{{ $user->name }}</div>
                        <div class="mini-email">{{ $user->email }}</div>
                    </div>
                    @if((int)($user->company_role_num ?? 0) === 0)
                        <span class="mini-badge full">Full</span>
                    @else
                        <span class="mini-badge role">R{{ $user->company_role_num }}</span>
                    @endif
                </div>
            @empty
                <div style="padding:22px 20px;text-align:center;color:#94a3b8;font-size:13px">
                    No users assigned.
                </div>
            @endforelse
        </div>

        {{-- Roles --}}
        <div class="cd-panel">
            <div class="cd-panel-head">
                <div><h3>Roles</h3><p>Defined access roles</p></div>
                <a href="{{ route('company.roles.index') }}">Manage</a>
            </div>
            <div class="tag-cloud">
                @forelse($roles as $role)
                    <span>{{ $role->name }}</span>
                @empty
                    <span style="color:#94a3b8;font-size:13px;padding:4px 0">No roles defined yet.</span>
                @endforelse
            </div>
        </div>

        {{-- Expense Types --}}
        <div class="cd-panel">
            <div class="cd-panel-head">
                <div><h3>Expense Types</h3><p>Cost categories</p></div>
                <a href="{{ route('company.expense-types.index') }}">Manage</a>
            </div>
            <div class="tag-cloud">
                @forelse($expenseTypes as $type)
                    <span style="background:#ccfbf1;color:#0d9488">{{ $type->name }}</span>
                @empty
                    <span style="color:#94a3b8;font-size:13px;padding:4px 0">No types defined yet.</span>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
