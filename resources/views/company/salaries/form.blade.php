@extends('layouts.app')
@section('title','Edit Salary Record')
@section('page_title','Edit Salary Record')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST" action="{{ route('company.salaries.update',$salary) }}">
            @csrf @method('PUT')
            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-cash"></i></div>
                    <div>
                        <h6>{{ $salary->employee?->name }} &mdash; {{ $salary->monthLabel() }}</h6>
                        <p>Adjust earnings, deductions and payment status</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="employee_id">Employee <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id" required>
                            @foreach($employees as $id => $name)
                                <option value="{{ $id }}" @selected((int) old('employee_id',$salary->employee_id) === (int) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="month">Salary Month <span class="text-danger">*</span></label>
                        <input class="form-control @error('month') is-invalid @enderror" id="month" name="month" type="month"
                               value="{{ old('month',$salary->month?->format('Y-m')) }}" required>
                        @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="basic_salary">Basic Salary <span class="text-danger">*</span></label>
                        <input class="form-control @error('basic_salary') is-invalid @enderror" id="basic_salary" name="basic_salary"
                               type="number" step="0.01" min="0" value="{{ old('basic_salary',$salary->basic_salary) }}" required>
                        @error('basic_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="allowances">Allowances</label>
                        <input class="form-control" id="allowances" name="allowances" type="number" step="0.01" min="0"
                               value="{{ old('allowances',$salary->allowances) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="bonus">Bonus</label>
                        <input class="form-control" id="bonus" name="bonus" type="number" step="0.01" min="0"
                               value="{{ old('bonus',$salary->bonus) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="overtime">Overtime</label>
                        <input class="form-control" id="overtime" name="overtime" type="number" step="0.01" min="0"
                               value="{{ old('overtime',$salary->overtime) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="deductions">Deductions</label>
                        <input class="form-control" id="deductions" name="deductions" type="number" step="0.01" min="0"
                               value="{{ old('deductions',$salary->deductions) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="advance">Advance</label>
                        <input class="form-control" id="advance" name="advance" type="number" step="0.01" min="0"
                               value="{{ old('advance',$salary->advance) }}">
                    </div>

                    <div class="col-12">
                        <div class="info-strip d-flex align-items-center justify-content-between gap-2 flex-wrap">
                            <span class="text-muted fs-13">Net payable</span>
                            <strong class="fs-16" id="net-preview">{{ $symbol }} 0.00</strong>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" @selected(old('status',$salary->status) === 'pending')>Pending</option>
                            <option value="paid" @selected(old('status',$salary->status) === 'paid')>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="payment_method">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">Not set</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}" @selected(old('payment_method',$salary->payment_method) === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="paid_date">Paid Date</label>
                        <input class="form-control @error('paid_date') is-invalid @enderror" id="paid_date" type="date" name="paid_date"
                               value="{{ old('paid_date',$salary->paid_date?->format('Y-m-d')) }}">
                        @error('paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Late arrivals, commission, loan adjustment...">{{ old('notes',$salary->notes) }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save Record</button>
                <a class="btn btn-light" href="{{ route('company.salaries.index') }}">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header border-light"><h6 class="mb-0">Record Info</h6></div>
            <div class="card-body d-grid gap-2 fs-13">
                <div class="d-flex justify-content-between gap-2">
                    <span class="text-muted">Generated</span>
                    <strong>{{ $salary->created_at?->format('d M Y H:i') ?? '—' }}</strong>
                </div>
                <div class="d-flex justify-content-between gap-2">
                    <span class="text-muted">Generated by</span>
                    <strong>{{ $salary->generator?->name ?? 'System' }}</strong>
                </div>
                <div class="d-flex justify-content-between gap-2">
                    <span class="text-muted">Last update</span>
                    <strong>{{ $salary->updated_at?->format('d M Y H:i') ?? '—' }}</strong>
                </div>
                <hr class="my-1">
                <a class="btn btn-soft-secondary btn-sm" href="{{ route('company.salaries.show',$salary) }}">
                    <i class="ti ti-receipt me-1"></i>View payslip
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-light"><h6 class="mb-0 text-danger">Danger Zone</h6></div>
            <div class="card-body">
                <p class="fs-13 text-muted">Deleting removes this month's record. You can generate it again any time.</p>
                <form method="POST" action="{{ route('company.salaries.destroy',$salary) }}"
                      onsubmit="return confirm('Delete this salary record?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-soft-danger btn-sm" type="submit"><i class="ti ti-trash me-1"></i>Delete Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const symbol = @json($symbol);
    const ids = ['basic_salary', 'allowances', 'bonus', 'overtime', 'deductions', 'advance'];
    const fields = ids.map((id) => document.getElementById(id));
    const preview = document.getElementById('net-preview');

    const update = () => {
        const values = fields.map((field) => parseFloat(field.value) || 0);
        const net = values[0] + values[1] + values[2] + values[3] - values[4] - values[5];
        preview.textContent = symbol + ' ' + net.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    fields.forEach((field) => field.addEventListener('input', update));
    update();
});
</script>
@endpush
