@extends('layouts.app')
@section('title','Salary Records')
@section('page_title','Salary Records')
@section('content')

@php
    $selectedMonth = request('month');
    $monthLabel = fn ($value) => $value ? \Carbon\Carbon::createFromFormat('Y-m', $value)->format('F Y') : '';
    $monthOptions = $selectedMonth && !$months->contains($selectedMonth) ? $months->prepend($selectedMonth) : $months;
@endphp

<div class="row g-3 mb-3">
    <div class="col-md-3 col-6">
        <div class="card mb-0">
            <div class="card-body">
                <p class="text-muted fs-12 text-uppercase mb-1">Records</p>
                <h4 class="mb-0">{{ (int) $summary->records }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card mb-0">
            <div class="card-body">
                <p class="text-muted fs-12 text-uppercase mb-1">Net Payable</p>
                <h4 class="mb-0">{{ $symbol }} {{ number_format($summary->net,2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card mb-0">
            <div class="card-body">
                <p class="text-muted fs-12 text-uppercase mb-1">Paid</p>
                <h4 class="mb-0 text-success">{{ $symbol }} {{ number_format($summary->paid,2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card mb-0">
            <div class="card-body">
                <p class="text-muted fs-12 text-uppercase mb-1">Pending</p>
                <h4 class="mb-0 text-warning">{{ $symbol }} {{ number_format($summary->pending,2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card" data-panel-no-search>
            <div class="card-header border-light justify-content-between">
                <form class="d-flex align-items-end gap-2 flex-wrap" method="GET">
                    <div style="min-width:170px">
                        <label class="form-label" for="filter-month">Month</label>
                        <select class="form-select" id="filter-month" name="month">
                            <option value="">All months</option>
                            @foreach($monthOptions as $value)
                                <option value="{{ $value }}" @selected($selectedMonth === $value)>{{ $monthLabel($value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:200px">
                        <label class="form-label" for="filter-employee">Employee</label>
                        <select class="form-select" id="filter-employee" name="employee_id" data-toggle="select2" data-placeholder="All employees">
                            <option value="">All employees</option>
                            @foreach($employees as $id => $name)
                                <option value="{{ $id }}" @selected((string) request('employee_id') === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="min-width:150px">
                        <label class="form-label" for="filter-status">Pay Status</label>
                        <select class="form-select" id="filter-status" name="status">
                            <option value="">All</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="ti ti-filter me-1"></i>Filter</button>
                    <a class="btn btn-light" href="{{ route('company.salaries.index') }}">Reset</a>
                </form>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('company.salaries.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-cash fs-sm me-2"></i>Generate Salary
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Employee</th>
                            <th>Month</th>
                            <th class="text-end">Basic</th>
                            <th class="text-end">Allowances</th>
                            <th class="text-end">Bonus / OT</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net</th>
                            <th>Pay Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary fw-semibold">
                                                {{ strtoupper(substr($salary->employee?->name ?? '?',0,1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $salary->employee?->name ?? '—' }}</h6>
                                            <p class="cell-sub">{{ $salary->employee?->employee_code }} &middot; {{ $salary->employee?->designation ?: 'Employee' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">{{ $salary->monthLabel() }}</span></td>
                                <td class="text-end">{{ number_format($salary->basic_salary,2) }}</td>
                                <td class="text-end">{{ number_format($salary->allowances,2) }}</td>
                                <td class="text-end">{{ number_format($salary->bonus + $salary->overtime,2) }}</td>
                                <td class="text-end">{{ number_format($salary->deductions + $salary->advance,2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($salary->net_salary,2) }}</td>
                                <td>
                                    @if($salary->isPaid())
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Paid</span>
                                        @if($salary->paid_date)<p class="cell-sub mb-0">{{ $salary->paid_date->format('d M Y') }}</p>@endif
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold"><i class="ti ti-clock me-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <a class="btn btn-soft-secondary btn-sm" href="{{ route('company.salaries.show',$salary) }}" title="Payslip">
                                        <i class="ti ti-receipt"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.salaries.edit',$salary) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    @if(!$salary->isPaid())
                                        <form method="POST" action="{{ route('company.salaries.mark-paid',$salary) }}" class="d-inline"
                                              onsubmit="return confirm('Mark {{ $salary->employee?->name }} as paid for {{ $salary->monthLabel() }}?');">
                                            @csrf
                                            <button class="btn btn-soft-success btn-sm" type="submit" title="Mark as paid">
                                                <i class="ti ti-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('company.salaries.destroy',$salary) }}" class="d-inline"
                                          onsubmit="return confirm('Delete this salary record?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-sm" type="submit" title="Delete">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="table-empty">
                                        <i class="ti ti-cash-off"></i>
                                        <p>No salary records found.</p>
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

            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12">{{ $salaries->total() }} record{{ $salaries->total() !== 1 ? 's' : '' }}</span>
                @if($salaries->hasPages()){{ $salaries->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
