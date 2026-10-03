@extends('layouts.app')
@section('title','Generate Salary')
@section('page_title','Generate Salary')
@section('content')
<div class="row">
    <div class="col-12">
        <form method="POST" action="{{ route('company.salaries.store') }}">
            @csrf
            <div class="card" data-panel-no-search>
                <div class="card-header border-light justify-content-between">
                    <div class="d-flex align-items-end gap-2 flex-wrap" data-panel-tools>
                        <div>
                            <label class="form-label mb-1" for="month">Salary Month <span class="text-danger">*</span></label>
                            <input class="form-control @error('month') is-invalid @enderror" id="month" name="month" type="month"
                                   value="{{ old('month',$month) }}" max="{{ now()->addYear()->format('Y-m') }}" required style="min-width:180px">
                            @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="app-search">
                            <input data-panel-search type="search" class="form-control" placeholder="Search employees...">
                            <i class="ti ti-search app-search-icon text-muted"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fs-12">Already generated: {{ count($generated) }}</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-custom table-centered table-hover w-100 mb-0">
                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                            <tr class="text-uppercase fs-xxs">
                                <th style="width:1%">
                                    <input class="form-check-input" type="checkbox" id="select-all" title="Select all">
                                </th>
                                <th>Employee</th>
                                <th>Designation</th>
                                <th class="text-end">Basic</th>
                                <th class="text-end">Allowances</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Expected Net</th>
                                <th>Payroll</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                @php $already = in_array($employee->id, $generated, true); @endphp
                                <tr>
                                    <td>
                                        <input class="form-check-input employee-check" type="checkbox" name="employee_ids[]"
                                               value="{{ $employee->id }}" @disabled($already) @checked(!$already)
                                               data-net="{{ $employee->expectedNet() }}">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-md me-2">
                                                <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary fw-semibold">
                                                    {{ strtoupper(substr($employee->name,0,1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="cell-title">{{ $employee->name }}</h6>
                                                <p class="cell-sub">{{ $employee->employee_code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="cell-title">{{ $employee->designation ?: '—' }}</h6>
                                        <p class="cell-sub">{{ $employee->department ?: 'No department' }}</p>
                                    </td>
                                    <td class="text-end">{{ number_format($employee->basic_salary,2) }}</td>
                                    <td class="text-end">{{ number_format($employee->allowances,2) }}</td>
                                    <td class="text-end">{{ number_format($employee->deductions,2) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($employee->expectedNet(),2) }}</td>
                                    <td>
                                        @if($already)
                                            <span class="badge bg-info bg-opacity-10 text-info fw-semibold"><i class="ti ti-circle-check me-1"></i>Generated</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">Not generated</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="table-empty">
                                            <i class="ti ti-users"></i>
                                            <p>No active employees yet.</p>
                                            <a href="{{ route('company.employees.create') }}" class="btn btn-primary mt-3">
                                                <i class="ti ti-user-plus me-1"></i>Add Employee
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <span class="text-muted fs-12">
                        <span id="selected-count">0</span> selected &middot; total
                        <strong id="selected-total">{{ $symbol }} 0.00</strong>
                    </span>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light" href="{{ route('company.salaries.index') }}">Cancel</a>
                        <button class="btn btn-primary" type="submit"><i class="ti ti-cash me-1"></i>Generate Salary</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const symbol = @json($symbol);
    const monthInput = document.getElementById('month');
    const selectAll = document.getElementById('select-all');
    const boxes = Array.from(document.querySelectorAll('.employee-check'));
    const count = document.getElementById('selected-count');
    const total = document.getElementById('selected-total');

    const format = (value) => symbol + ' ' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const update = () => {
        const checked = boxes.filter((box) => box.checked);
        count.textContent = checked.length;
        total.textContent = format(checked.reduce((sum, box) => sum + (parseFloat(box.dataset.net) || 0), 0));
        if (selectAll) selectAll.checked = boxes.length > 0 && checked.length === boxes.filter((box) => !box.disabled).length;
    };

    boxes.forEach((box) => box.addEventListener('change', update));

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            boxes.forEach((box) => { if (!box.disabled) box.checked = selectAll.checked; });
            update();
        });
    }

    monthInput.addEventListener('change', () => {
        if (monthInput.value) window.location.search = 'month=' + encodeURIComponent(monthInput.value);
    });

    update();
});
</script>
@endpush
