@extends('layouts.app')
@section('title',$employee->exists ? 'Edit Employee' : 'Add Employee')
@section('page_title',$employee->exists ? 'Edit Employee' : 'Add Employee')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="mb-0" method="POST"
              action="{{ $employee->exists ? route('company.employees.update',$employee) : route('company.employees.store') }}">
            @csrf
            @if($employee->exists)@method('PUT')@endif

            <div class="card form-card">
                <div class="card-header border-light">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-user"></i></div>
                        <div>
                            <h6>Employee Details</h6>
                            <p>Personal and employment information</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   value="{{ old('name',$employee->name) }}" placeholder="e.g. Ali Raza" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="employee_code">Employee Code <span class="text-danger">*</span></label>
                            <input class="form-control @error('employee_code') is-invalid @enderror" id="employee_code" name="employee_code"
                                   value="{{ old('employee_code',$employee->employee_code) }}" placeholder="EMP-0001" required>
                            @error('employee_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone</label>
                            <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                   value="{{ old('phone',$employee->phone) }}" placeholder="+92 300 0000000">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email"
                                   value="{{ old('email',$employee->email) }}" placeholder="employee@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="cnic">CNIC / ID Number</label>
                            <input class="form-control @error('cnic') is-invalid @enderror" id="cnic" name="cnic"
                                   value="{{ old('cnic',$employee->cnic) }}" placeholder="35202-0000000-0">
                            @error('cnic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="joining_date">Joining Date</label>
                            <input class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" type="date" name="joining_date"
                                   value="{{ old('joining_date',$employee->joining_date?->format('Y-m-d')) }}">
                            @error('joining_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="designation">Designation</label>
                            <input class="form-control" id="designation" name="designation"
                                   value="{{ old('designation',$employee->designation) }}" placeholder="e.g. Sales Manager">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="department">Department</label>
                            <input class="form-control" id="department" name="department"
                                   value="{{ old('department',$employee->department) }}" placeholder="e.g. Sales">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="address">Address</label>
                            <input class="form-control" id="address" name="address"
                                   value="{{ old('address',$employee->address) }}" placeholder="Street, City">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card form-card mb-0">
                <div class="card-header border-light">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-cash"></i></div>
                        <div>
                            <h6>Salary Structure</h6>
                            <p>Fixed monthly amounts used when generating salary</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="basic_salary">Basic Salary ({{ $symbol }}) <span class="text-danger">*</span></label>
                            <input class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary"
                                   type="number" step="0.01" min="0" value="{{ old('basic_salary',$employee->basic_salary ?? 0) }}" required>
                            @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="allowances">Fixed Allowances ({{ $symbol }})</label>
                            <input class="form-control @error('allowances') is-invalid @enderror" id="allowances" name="allowances"
                                   type="number" step="0.01" min="0" value="{{ old('allowances',$employee->allowances ?? 0) }}">
                            @error('allowances')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="deductions">Fixed Deductions ({{ $symbol }})</label>
                            <input class="form-control @error('deductions') is-invalid @enderror" id="deductions" name="deductions"
                                   type="number" step="0.01" min="0" value="{{ old('deductions',$employee->deductions ?? 0) }}">
                            @error('deductions')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="info-strip d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                <span class="text-muted fs-13">Expected monthly net</span>
                                <strong class="fs-16" id="net-preview">{{ $symbol }} {{ number_format($employee->expectedNet(),2) }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="Contract terms, probation period, etc.">{{ old('notes',$employee->notes) }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active',$employee->exists ? $employee->is_active : true))>
                                <label class="form-check-label" for="is_active">Active employee</label>
                                <div class="form-text">Inactive employees are excluded from salary generation.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save Employee</button>
                    <a class="btn btn-light" href="{{ route('company.employees.index') }}">Cancel</a>
                </div>
            </div>
        </form>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header border-light"><h6 class="mb-0">How salary works</h6></div>
            <div class="card-body">
                <ol class="ps-3 mb-0 fs-13 text-muted d-grid gap-2">
                    <li>Add the employee with a basic salary, fixed allowances and fixed deductions.</li>
                    <li>Open <a href="{{ route('company.salaries.create') }}">Generate Salary</a>, pick a month and select employees.</li>
                    <li>Adjust bonus, overtime, advance or deductions on any generated record.</li>
                    <li>Mark records as paid, then review totals in the <a href="{{ route('company.reports.salaries') }}">Salary Report</a>.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const symbol = @json($symbol);
    const fields = ['basic_salary', 'allowances', 'deductions'].map((id) => document.getElementById(id));
    const preview = document.getElementById('net-preview');

    const update = () => {
        const [basic, allowances, deductions] = fields.map((field) => parseFloat(field.value) || 0);
        const net = basic + allowances - deductions;
        preview.textContent = symbol + ' ' + net.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    fields.forEach((field) => field.addEventListener('input', update));
    update();
});
</script>
@endpush
