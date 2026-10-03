@extends('layouts.app')
@section('title','Employee Profile')
@section('page_title','Employee Profile')
@section('content')
<div class="row">
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="avatar-lg mx-auto mb-3">
                    <span class="avatar-title rounded-circle bg-primary bg-opacity-10 text-primary fs-24 fw-semibold">
                        {{ strtoupper(substr($employee->name,0,1)) }}
                    </span>
                </div>
                <h5 class="mb-1">{{ $employee->name }}</h5>
                <p class="text-muted fs-13 mb-2">
                    {{ $employee->designation ?: 'Employee' }}
                    @if($employee->department) &middot; {{ $employee->department }} @endif
                </p>
                @if($employee->is_active)
                    <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                @else
                    <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                @endif
                <hr class="my-3">
                <div class="text-start d-grid gap-2 fs-13">
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted">Employee code</span><strong>{{ $employee->employee_code }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted">Joining date</span>
                        <strong>{{ $employee->joining_date?->format('d M Y') ?? '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted">Phone</span><strong>{{ $employee->phone ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted">Email</span><strong>{{ $employee->email ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted">CNIC</span><strong>{{ $employee->cnic ?: '—' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between gap-2">
                        <span class="text-muted">Address</span><strong class="text-end">{{ $employee->address ?: '—' }}</strong>
                    </div>
                </div>
                @if($employee->notes)
                    <hr class="my-3">
                    <p class="fs-13 text-muted text-start mb-0">{{ $employee->notes }}</p>
                @endif
            </div>
            <div class="card-footer d-flex gap-2">
                <a class="btn btn-primary btn-sm" href="{{ route('company.employees.edit',$employee) }}">
                    <i class="ti ti-pencil me-1"></i>Edit
                </a>
                <a class="btn btn-soft-success btn-sm" href="{{ route('company.salaries.create',['month' => now()->format('Y-m')]) }}">
                    <i class="ti ti-cash me-1"></i>Generate Salary
                </a>
                <form method="POST" action="{{ route('company.employees.destroy',$employee) }}" class="ms-auto"
                      onsubmit="return confirm('Delete {{ $employee->name }}? This also removes their salary history.');">
                    @csrf @method('DELETE')
                    <button class="btn btn-soft-danger btn-sm" type="submit"><i class="ti ti-trash me-1"></i>Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="row g-3 mb-3">
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="text-muted fs-12 text-uppercase mb-1">Expected Net</p>
                        <h4 class="mb-0">{{ $symbol }} {{ number_format($employee->expectedNet(),2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="text-muted fs-12 text-uppercase mb-1">Total Paid</p>
                        <h4 class="mb-0 text-success">{{ $symbol }} {{ number_format($totals->paid_net,2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="text-muted fs-12 text-uppercase mb-1">Pending</p>
                        <h4 class="mb-0 text-warning">{{ $symbol }} {{ number_format($totals->pending_net,2) }}</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card mb-0">
                    <div class="card-body">
                        <p class="text-muted fs-12 text-uppercase mb-1">Records</p>
                        <h4 class="mb-0">{{ (int) $totals->records }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-light justify-content-between">
                <h6 class="mb-0">Salary History</h6>
                <a class="btn btn-soft-primary btn-sm" href="{{ route('company.salaries.index',['employee_id' => $employee->id]) }}">
                    View all records
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Month</th>
                            <th class="text-end">Earnings</th>
                            <th class="text-end">Deductions</th>
                            <th class="text-end">Net</th>
                            <th>Pay Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                            @php
                                $earnings = $salary->basic_salary + $salary->allowances + $salary->bonus + $salary->overtime;
                                $cuts = $salary->deductions + $salary->advance;
                            @endphp
                            <tr>
                                <td><h6 class="cell-title">{{ $salary->monthLabel() }}</h6></td>
                                <td class="text-end">{{ $symbol }} {{ number_format($earnings,2) }}</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($cuts,2) }}</td>
                                <td class="text-end fw-semibold">{{ $symbol }} {{ number_format($salary->net_salary,2) }}</td>
                                <td>
                                    @if($salary->isPaid())
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Paid</span>
                                        @if($salary->paid_date)<p class="cell-sub mb-0">{{ $salary->paid_date->format('d M Y') }}</p>@endif
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold"><i class="ti ti-clock me-1"></i>Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-secondary btn-sm" href="{{ route('company.salaries.show',$salary) }}" title="Payslip">
                                        <i class="ti ti-receipt"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.salaries.edit',$salary) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="table-empty">
                                        <i class="ti ti-cash-off"></i>
                                        <p>No salary generated for this employee yet.</p>
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
                <span class="text-muted fs-12" data-panel-count></span>
                @if($salaries->hasPages()){{ $salaries->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
