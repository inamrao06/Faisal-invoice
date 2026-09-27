@extends('layouts.app')
@section('title', 'Expense Report')
@section('page_title', 'Expense Report')
@section('content')
    @php
        $company = auth()->user()->company;
        $currency = $company?->currency?->symbol ?? 'PKR';
        $hasFilters = request()->filled('from') || request()->filled('to') || request()->filled('expense_head_id');
    @endphp

    

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
                    <table class="table report-table align-middle" data-dx-grid>
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
