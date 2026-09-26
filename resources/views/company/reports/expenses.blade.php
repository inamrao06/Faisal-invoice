@extends('layouts.app')
@section('title', 'Expense Report')
@section('page_title', 'Expense Report')
@section('content')
    @php
        $company = auth()->user()->company;
        $currency = $company?->currency?->symbol ?? 'PKR';
        $hasFilters = request()->filled('from') || request()->filled('to') || request()->filled('expense_head_id');
    @endphp

    <style>
        .report-page{max-width:1180px;margin:0 auto}
        .report-shell{background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:10px;box-shadow:var(--bs-box-shadow-sm);overflow:hidden}
        .report-hero{padding:24px 26px;display:grid;grid-template-columns:minmax(0,1fr) 420px;gap:24px;align-items:center;border-bottom:1px solid var(--bs-border-color)}
        .report-title h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
        .report-title p{margin:4px 0 0;font-size:13px;color:#64748b}
        .report-kpis{display:grid;grid-template-columns:1fr 1.4fr;gap:12px}
        .kpi{border:1px solid var(--bs-border-color);border-radius:8px;padding:14px 16px;background:var(--bs-tertiary-bg)}
        .kpi span{display:block;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:5px}
        .kpi strong{font-size:24px;line-height:1.15;color:#0f172a}
        .filter-bar{padding:16px 26px;border-bottom:1px solid var(--bs-border-color);background:#fbfcfe}
        .filter-grid{display:grid;grid-template-columns:170px 170px minmax(240px,1fr) auto;gap:12px;align-items:end}
        .filter-grid label{display:block;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:6px}
        .filter-actions{display:flex;gap:8px}
        .active-filters{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
        .filter-chip{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:rgba(var(--bs-primary-rgb),.09);color:var(--bs-primary);font-size:12px;font-weight:700;padding:5px 10px}
        .report-table-wrap{padding:0}
        .report-table{margin:0}
        .report-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:var(--bs-tertiary-bg);padding:13px 18px;white-space:nowrap;border-bottom:1px solid var(--bs-border-color)}
        .report-table tbody td{padding:14px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
        .report-table tbody tr:hover td{background:#fafbff}
        .invoice-link{display:inline-flex;align-items:center;gap:10px;color:#0f172a;font-weight:800}
        .invoice-icon{width:34px;height:34px;border-radius:8px;background:rgba(var(--bs-primary-rgb),.1);color:var(--bs-primary);display:grid;place-items:center;flex:0 0 auto}
        .type-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;background:#f1f5f9;color:#475569;padding:5px 10px;font-size:12px;font-weight:800}
        .amount{font-weight:900;color:#0f172a;white-space:nowrap}
        .report-foot{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:15px 18px;background:var(--bs-tertiary-bg);border-top:1px solid var(--bs-border-color);flex-wrap:wrap}
        .report-foot strong{font-size:16px;color:#0f172a}
        .empty-report{padding:54px 20px;text-align:center;color:#94a3b8}
        .empty-report i{font-size:42px;display:block;margin-bottom:10px;opacity:.6}
        @media(max-width:991.98px){.report-hero{grid-template-columns:1fr}.report-kpis{max-width:520px}.filter-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.filter-actions{grid-column:span 2}}
        @media(max-width:575.98px){.report-hero,.filter-bar{padding:20px}.report-kpis,.filter-grid{grid-template-columns:1fr}.filter-actions{grid-column:span 1}.filter-actions .btn{flex:1}}
    </style>

    <div class="report-page">
        <div class="report-shell">
            <div class="report-hero">
                <div class="report-title">
                    <h2>Expense Report</h2>
                    <p>{{ $company?->name ?? 'Company' }} expense activity by date range and expense type.</p>
                </div>
                <div class="report-kpis">
                    <div class="kpi">
                        <span>Records</span>
                        <strong>{{ number_format($summary->records) }}</strong>
                    </div>
                    <div class="kpi">
                        <span>Total Amount</span>
                        <strong>{{ $currency }} {{ number_format($summary->total, 2) }}</strong>
                    </div>
                </div>
            </div>

            <form class="filter-bar" method="GET" action="{{ route('company.reports.expenses') }}">
                <div class="filter-grid">
                    <div>
                        <label for="from">From</label>
                        <input id="from" class="form-control" type="date" name="from" value="{{ request('from') }}">
                    </div>
                    <div>
                        <label for="to">To</label>
                        <input id="to" class="form-control" type="date" name="to" value="{{ request('to') }}">
                    </div>
                    <div>
                        <label for="expense_head_id">Expense Type</label>
                        <select id="expense_head_id" class="form-select" name="expense_head_id" data-toggle="select2" data-placeholder="All expense types">
                            <option value="">All expense types</option>
                            @foreach ($heads as $head)
                                <option value="{{ $head->id }}" @selected((string) request('expense_head_id') === (string) $head->id)>
                                    {{ $head->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button class="btn btn-primary" type="submit"><i class="ti ti-filter me-1"></i>Apply</button>
                        @if ($hasFilters)
                            <a class="btn btn-light" href="{{ route('company.reports.expenses') }}">Clear</a>
                        @endif
                    </div>
                </div>

                @if($hasFilters)
                    <div class="active-filters">
                        @if(request('from'))<span class="filter-chip"><i class="ti ti-calendar"></i>From {{ request('from') }}</span>@endif
                        @if(request('to'))<span class="filter-chip"><i class="ti ti-calendar"></i>To {{ request('to') }}</span>@endif
                        @if(request('expense_head_id'))
                            <span class="filter-chip"><i class="ti ti-tag"></i>{{ $heads->firstWhere('id', (int) request('expense_head_id'))?->name ?? 'Selected type' }}</span>
                        @endif
                    </div>
                @endif
            </form>

            <div class="report-table-wrap">
                <div class="table-responsive">
                    <table class="table report-table align-middle">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Expense Type</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($expenses as $expense)
                                <tr>
                                    <td>
                                        <a class="invoice-link" href="{{ route('company.expenses.show', $expense) }}">
                                            <span class="invoice-icon"><i class="ti ti-receipt-2"></i></span>
                                            <span>{{ $expense->invoice_no }}</span>
                                        </a>
                                    </td>
                                    <td style="white-space:nowrap">{{ $expense->expense_date->format('d M Y') }}</td>
                                    <td><span class="type-pill"><i class="ti ti-tag"></i>{{ $expense->head?->name ?? '-' }}</span></td>
                                    <td>{{ str($expense->payment_method)->replace('_', ' ')->title() }}</td>
                                    <td><span class="status {{ in_array($expense->status, ['paid','approved','completed']) ? 'on' : 'off' }}">{{ str($expense->status)->title() }}</span></td>
                                    <td class="text-end amount">{{ $currency }} {{ number_format($expense->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-report">
                                            <i class="ti ti-report-search"></i>
                                            No expenses found for the selected filters.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="report-foot">
                    <span class="text-muted">{{ number_format($summary->records) }} record{{ (int) $summary->records === 1 ? '' : 's' }}</span>
                    <strong>Total: {{ $currency }} {{ number_format($summary->total, 2) }}</strong>
                </div>
                @if ($expenses->hasPages())
                    <div class="p-3 border-top">{{ $expenses->links() }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection
