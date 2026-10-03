@extends('layouts.app')
@section('title','Salary Report')
@section('page_title','Salary Report')
@section('content')

@php
    $monthLabel = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('F Y') : '';
@endphp

<div class="row">
    <div class="col-12">
        <div class="card" data-panel-no-search>
            <div class="card-header border-light justify-content-between">
                <form class="d-flex align-items-end gap-2 flex-wrap" method="GET">
                    <div>
                        <label class="form-label mb-1" for="from">From Month</label>
                        <input class="form-control" id="from" type="month" name="from" value="{{ $from }}" style="min-width:170px">
                    </div>
                    <div>
                        <label class="form-label mb-1" for="to">To Month</label>
                        <input class="form-control" id="to" type="month" name="to" value="{{ $to }}" style="min-width:170px">
                    </div>
                    <div style="min-width:200px">
                        <label class="form-label mb-1" for="employee_id">Employee</label>
                        <select class="form-select" id="employee_id" name="employee_id" data-toggle="select2" data-placeholder="All employees">
                            <option value="">All employees</option>
                            @foreach($employees as $id => $name)
                                <option value="{{ $id }}" @selected((string) request('employee_id') === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:150px">
                        <label class="form-label mb-1" for="status">Pay Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="ti ti-filter me-1"></i>Apply</button>
                    <a class="btn btn-light" href="{{ route('company.reports.salaries') }}">Reset</a>
                </form>
                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-soft-secondary" type="button" onclick="window.print()">
                        <i class="ti ti-printer fs-sm me-2"></i>Print
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted fs-12 text-uppercase mb-1">Records</p>
                            <h4 class="mb-0">{{ (int) $totals->records }}</h4>
                            <p class="cell-sub mb-0">{{ $byMonth->count() }} month(s)</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted fs-12 text-uppercase mb-1">Total Net</p>
                            <h4 class="mb-0">{{ $symbol }} {{ number_format($totals->net,2) }}</h4>
                            <p class="cell-sub mb-0">Earnings {{ $symbol }} {{ number_format($totals->basic + $totals->allowances + $totals->bonus + $totals->overtime,2) }}</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted fs-12 text-uppercase mb-1">Paid</p>
                            <h4 class="mb-0 text-success">{{ $symbol }} {{ number_format($totals->paid,2) }}</h4>
                            <p class="cell-sub mb-0">Deductions {{ $symbol }} {{ number_format($totals->deductions + $totals->advance,2) }}</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <p class="text-muted fs-12 text-uppercase mb-1">Pending</p>
                            <h4 class="mb-0 text-warning">{{ $symbol }} {{ number_format($totals->pending,2) }}</h4>
                            <p class="cell-sub mb-0">Bonus {{ $symbol }} {{ number_format($totals->bonus,2) }} &middot; OT {{ $symbol }} {{ number_format($totals->overtime,2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" data-panel-no-search>
            <div class="card-header border-light"><h6 class="mb-0">Month-wise Summary</h6></div>
            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Month</th>
                            <th class="text-center">Employees</th>
                            <th class="text-end">Earnings</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Pending</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byMonth as $row)
                            <tr>
                                <td>
                                    <a class="fw-semibold" href="{{ route('company.salaries.index',['month' => \Carbon\Carbon::parse($row->month)->format('Y-m')]) }}">
                                        {{ $monthLabel($row->month) }}
                                    </a>
                                </td>
                                <td class="text-center">{{ (int) $row->employees }}</td>
                                <td class="text-end">{{ number_format($row->earnings,2) }}</td>
                                <td class="text-end">{{ number_format($row->deductions,2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row->net,2) }}</td>
                                <td class="text-end text-success">{{ number_format($row->paid,2) }}</td>
                                <td class="text-end text-warning">{{ number_format($row->pending,2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="table-empty">
                                        <i class="ti ti-report"></i>
                                        <p>No salary data for the selected filters.</p>
                                        <a href="{{ route('company.salaries.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-cash me-1"></i>Generate Salary
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" data-panel-no-search>
            <div class="card-header border-light">
                <h6 class="mb-0">Salary Details</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Employee</th>
                            <th>Month</th>
                            <th class="text-end">Basic</th>
                            <th class="text-end">Allowances</th>
                            <th class="text-end">Bonus</th>
                            <th class="text-end">Overtime</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Advance</th>
                            <th class="text-end">Net</th>
                            <th>Pay Status</th>
                            <th class="text-center" style="width:1%">Slip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                            <tr>
                                <td>
                                    <h6 class="cell-title">{{ $salary->employee?->name ?? '—' }}</h6>
                                    <p class="cell-sub">{{ $salary->employee?->employee_code }}</p>
                                </td>
                                <td>{{ $salary->monthLabel() }}</td>
                                <td class="text-end">{{ number_format($salary->basic_salary,2) }}</td>
                                <td class="text-end">{{ number_format($salary->allowances,2) }}</td>
                                <td class="text-end">{{ number_format($salary->bonus,2) }}</td>
                                <td class="text-end">{{ number_format($salary->overtime,2) }}</td>
                                <td class="text-end">{{ number_format($salary->deductions,2) }}</td>
                                <td class="text-end">{{ number_format($salary->advance,2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($salary->net_salary,2) }}</td>
                                <td>
                                    @if($salary->isPaid())
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">Paid</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-secondary btn-sm" href="{{ route('company.salaries.show',$salary) }}" title="Payslip">
                                        <i class="ti ti-receipt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="table-empty">
                                        <i class="ti ti-cash-off"></i>
                                        <p>No salary records found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12">{{ $salaries->total() }} record{{ $salaries->total() !== 1 ? 's' : '' }}</span>
                @if($salaries->hasPages()){{ $salaries->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
