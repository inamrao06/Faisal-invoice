@extends('layouts.app')
@section('title','Payslip')
@section('page_title','Payslip')
@section('content')

@php
    $company = auth()->user()->company;
    $earnings = $salary->basic_salary + $salary->allowances + $salary->bonus + $salary->overtime;
    $cuts = $salary->deductions + $salary->advance;
@endphp

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-receipt"></i></div>
                    <div>
                        <h6>Salary Slip &mdash; {{ $salary->monthLabel() }}</h6>
                        <p>{{ $company?->name ?? config('app.name') }}</p>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-soft-secondary btn-sm" type="button" onclick="window.print()">
                        <i class="ti ti-printer me-1"></i>Print
                    </button>
                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.salaries.edit',$salary) }}">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg me-2">
                                <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary fs-20 fw-semibold">
                                    {{ strtoupper(substr($salary->employee?->name ?? '?',0,1)) }}
                                </span>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $salary->employee?->name }}</h5>
                                <p class="cell-sub mb-0">
                                    {{ $salary->employee?->employee_code }}
                                    @if($salary->employee?->designation) &middot; {{ $salary->employee->designation }} @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        @if($salary->isPaid())
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Paid</span>
                            <p class="cell-sub mt-2 mb-0">
                                {{ $salary->paid_date?->format('d M Y') ?? '—' }}
                                @if($salary->payment_method) &middot; {{ $salary->payment_method }} @endif
                            </p>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold"><i class="ti ti-clock me-1"></i>Pending</span>
                        @endif
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                            <tr class="text-uppercase fs-xxs">
                                <th>Earnings</th>
                                <th class="text-end">Amount</th>
                                <th>Deductions</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($salary->basic_salary,2) }}</td>
                                <td>Fixed Deductions</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($salary->deductions,2) }}</td>
                            </tr>
                            <tr>
                                <td>Allowances</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($salary->allowances,2) }}</td>
                                <td>Advance</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($salary->advance,2) }}</td>
                            </tr>
                            <tr>
                                <td>Bonus</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($salary->bonus,2) }}</td>
                                <td class="text-muted">Total Deductions</td>
                                <td class="text-end fw-semibold">{{ $symbol }} {{ number_format($cuts,2) }}</td>
                            </tr>
                            <tr>
                                <td>Overtime</td>
                                <td class="text-end">{{ $symbol }} {{ number_format($salary->overtime,2) }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="bg-light bg-opacity-50">
                                <td class="fw-semibold">Total Earnings</td>
                                <td class="text-end fw-semibold">{{ $symbol }} {{ number_format($earnings,2) }}</td>
                                <td class="fw-semibold">Net Payable</td>
                                <td class="text-end fw-bold text-primary">{{ $symbol }} {{ number_format($salary->net_salary,2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($salary->notes)
                    <div class="mt-3">
                        <h6 class="fs-13 text-uppercase text-muted">Notes</h6>
                        <p class="fs-13 mb-0">{{ $salary->notes }}</p>
                    </div>
                @endif
            </div>
            <div class="card-footer d-flex gap-2 flex-wrap">
                @if(!$salary->isPaid())
                    <form method="POST" action="{{ route('company.salaries.mark-paid',$salary) }}"
                          onsubmit="return confirm('Mark this salary as paid?');">
                        @csrf
                        <button class="btn btn-success btn-sm" type="submit"><i class="ti ti-check me-1"></i>Mark as Paid</button>
                    </form>
                @endif
                <a class="btn btn-light btn-sm" href="{{ route('company.salaries.index') }}">Back to records</a>
                <a class="btn btn-light btn-sm" href="{{ route('company.employees.show',$salary->employee_id) }}">Employee profile</a>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header border-light"><h6 class="mb-0">Payment Details</h6></div>
            <div class="card-body d-grid gap-2 fs-13">
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Month</span><strong>{{ $salary->monthLabel() }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Net payable</span><strong>{{ $symbol }} {{ number_format($salary->net_salary,2) }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Status</span><strong>{{ ucfirst($salary->status) }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Payment method</span><strong>{{ $salary->payment_method ?: '—' }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Paid date</span><strong>{{ $salary->paid_date?->format('d M Y') ?? '—' }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Generated by</span><strong>{{ $salary->generator?->name ?? 'System' }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Generated on</span><strong>{{ $salary->created_at?->format('d M Y') ?? '—' }}</strong></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-light"><h6 class="mb-0">Employee</h6></div>
            <div class="card-body d-grid gap-2 fs-13">
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Designation</span><strong>{{ $salary->employee?->designation ?: '—' }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Department</span><strong>{{ $salary->employee?->department ?: '—' }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Phone</span><strong>{{ $salary->employee?->phone ?: '—' }}</strong></div>
                <div class="d-flex justify-content-between gap-2"><span class="text-muted">Joining</span><strong>{{ $salary->employee?->joining_date?->format('d M Y') ?? '—' }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
