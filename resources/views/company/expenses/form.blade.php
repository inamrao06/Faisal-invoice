@extends('layouts.app')
@section('title', $expense->exists ? 'Edit Expense' : 'Create Expense')
@section('page_title', $expense->exists ? 'Edit Expense' : 'Create Expense')
@section('content')
<style>
.frm-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px}
.frm-head h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
.frm-head p{margin:3px 0 0;font-size:13px;color:#64748b}
.frm-card{background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:var(--bs-border-radius);box-shadow:var(--bs-box-shadow-sm);overflow:hidden;margin-bottom:20px}
.frm-card-head{padding:16px 20px;border-bottom:1px solid var(--bs-border-color);display:flex;align-items:center;gap:10px}
.frm-card-head h3{margin:0;font-size:15px;font-weight:700}
.frm-card-body{padding:20px}
.form-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.form-grid .span-2{grid-column:span 2}
.form-grid .span-3{grid-column:span 3}
.form-grid label{display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
@media(max-width:767.98px){.form-grid{grid-template-columns:1fr}.form-grid .span-2,.form-grid .span-3{grid-column:span 1}}
.items-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:#f8fafc;padding:10px 12px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.items-table tbody td{padding:8px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.items-table .line-total{font-weight:700;color:#0f172a;white-space:nowrap}
.items-table .btn-remove{color:#dc2626}
.totals-box{max-width:340px;width:100%}
.totals-box .row-line{display:flex;justify-content:space-between;padding:8px 4px;font-size:14px;color:#475569;border-bottom:1px dashed var(--bs-border-color)}
.totals-box .row-line:last-child{border-bottom:0}
.totals-box .grand{font-size:17px;font-weight:800;color:#0f172a}
.form-actions{display:flex;gap:10px;align-items:center;padding:16px 20px;border-top:1px solid var(--bs-border-color);background:var(--bs-tertiary-bg)}
</style>

<form method="POST" action="{{ $expense->exists ? route('company.expenses.update', $expense) : route('company.expenses.store') }}" enctype="multipart/form-data">
    @csrf
    @if($expense->exists) @method('PUT') @endif

    <div class="frm-head">
        <div>
            <h2>{{ $expense->exists ? 'Edit Expense Invoice' : 'Expense Invoice' }}</h2>
            <p>{{ $expense->exists ? 'Update expense details and line items.' : 'Record a new company expense with line items.' }}</p>
        </div>
        @if($expense->exists)
            <a class="btn btn-light" href="{{ route('company.expenses.show', $expense) }}"><i class="ti ti-arrow-left me-1"></i>Back to Invoice</a>
        @endif
    </div>

    <div class="frm-card">
        <div class="frm-card-head"><i class="ti ti-receipt-2 text-primary"></i><h3>Expense Details</h3></div>
        <div class="frm-card-body">
            <div class="form-grid">
                <div>
                    <label for="expense_date">Date *</label>
                    <input id="expense_date" class="form-control @error('expense_date') is-invalid @enderror" type="date" name="expense_date" value="{{ old('expense_date', $expense->expense_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="payment_method">Payment *</label>
                    <select id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" name="payment_method" required>
                        <option value="">Select payment…</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->code }}" @selected(old('payment_method', $expense->payment_method) === $method->code)>{{ $method->name }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="expense_head_id">Expense Head *</label>
                    <select id="expense_head_id" class="form-select @error('expense_head_id') is-invalid @enderror" name="expense_head_id" required>
                        <option value="">Select head…</option>
                        @foreach($heads as $head)
                            <option value="{{ $head->id }}" @selected((int)old('expense_head_id', $expense->expense_head_id) === $head->id)>{{ $head->name }}</option>
                        @endforeach
                    </select>
                    @error('expense_head_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="span-2">
                    <label for="attachment">Attachment</label>
                    <input id="attachment" class="form-control @error('attachment') is-invalid @enderror" type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf">
                    @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">
                        Optional. JPG, PNG, WEBP or PDF, max 5 MB.
                        @if($expense->attachment_path)
                            Current attachment will be kept unless you upload a new one.
                        @endif
                    </div>
                </div>
                <div>
                    <label for="remarks">Remarks</label>
                    <textarea id="remarks" class="form-control @error('remarks') is-invalid @enderror" name="remarks" rows="1" placeholder="Enter remarks / description">{{ old('remarks', $expense->remarks) }}</textarea>
                    @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="warranty_provider_id">Warranty Provider</label>
                    <select id="warranty_provider_id" class="form-select @error('warranty_provider_id') is-invalid @enderror" name="warranty_provider_id">
                        <option value="">Select warranty...</option>
                        @foreach($warrantyProviders as $provider)
                            <option value="{{ $provider->id }}" @selected((int) old('warranty_provider_id', $expense->warranty_provider_id) === $provider->id)>{{ $provider->name }}</option>
                        @endforeach
                    </select>
                    @error('warranty_provider_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="warranty_duration_months">Warranty Duration</label>
                    <select id="warranty_duration_months" class="form-select @error('warranty_duration_months') is-invalid @enderror" name="warranty_duration_months">
                        <option value="">Select months...</option>
                        @foreach($warrantyDurations as $duration)
                            <option value="{{ $duration->months }}" @selected((string) old('warranty_duration_months', $expense->warranty_duration_months) === (string) $duration->months)>{{ $duration->name }}</option>
                        @endforeach
                    </select>
                    @error('warranty_duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="frm-card">
        <div class="frm-card-head d-flex justify-content-between">
            <span class="d-flex align-items-center gap-2"><i class="ti ti-list-details text-primary"></i><h3>Line Items</h3></span>
            <button type="button" class="btn btn-sm btn-primary" id="addLine"><i class="ti ti-plus"></i> Add Row</button>
        </div>
        <div class="table-responsive">
            <table class="table items-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width:240px">Description</th>
                        <th style="width:110px">Qty</th>
                        <th style="width:130px">Rate</th>
                        <th style="width:110px">Tax %</th>
                        <th style="width:130px" class="text-end">Total</th>
                        <th style="width:52px"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    @php
                        $oldItems = old('items', $expense->exists ? $expense->items->map(fn ($item) => [
                            'description' => $item->description,
                            'quantity' => $item->quantity,
                            'rate' => $item->rate,
                            'tax_percent' => $item->tax_percent,
                        ])->toArray() : []);
                    @endphp
                    @forelse($oldItems as $i => $row)
                        <tr class="item-row">
                            <td><input class="form-control" name="items[{{ $i }}][description]" value="{{ $row['description'] ?? '' }}" placeholder="Description" required></td>
                            <td><input class="form-control qty" type="number" step="0.01" min="0" name="items[{{ $i }}][quantity]" value="{{ $row['quantity'] ?? 1 }}" required></td>
                            <td><input class="form-control rate" type="number" step="0.01" min="0" name="items[{{ $i }}][rate]" value="{{ $row['rate'] ?? 0 }}" required></td>
                            <td><input class="form-control tax" type="number" step="0.01" min="0" max="100" name="items[{{ $i }}][tax_percent]" value="{{ $row['tax_percent'] ?? 0 }}"></td>
                            <td class="text-end line-total">0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-light btn-remove" title="Remove"><i class="ti ti-trash"></i></button></td>
                        </tr>
                    @empty
                        <tr class="item-row">
                            <td><input class="form-control" name="items[0][description]" value="" placeholder="Description" required></td>
                            <td><input class="form-control qty" type="number" step="0.01" min="0" name="items[0][quantity]" value="1" required></td>
                            <td><input class="form-control rate" type="number" step="0.01" min="0" name="items[0][rate]" value="0" required></td>
                            <td><input class="form-control tax" type="number" step="0.01" min="0" max="100" name="items[0][tax_percent]" value="0"></td>
                            <td class="text-end line-total">0.00</td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-light btn-remove" title="Remove"><i class="ti ti-trash"></i></button></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="frm-card-body d-flex justify-content-end">
            <div class="totals-box">
                <div class="row-line"><span>Subtotal:</span><span id="subtotal">0.00</span></div>
                <div class="row-line"><span>Total Tax:</span><span id="totalTax">0.00</span></div>
                <div class="row-line grand"><span>Total:</span><span id="grandTotal">0.00</span></div>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>{{ $expense->exists ? 'Update Expense' : 'Save Expense' }}</button>
        <a class="btn btn-light" href="{{ route('company.expenses.index') }}">Cancel</a>
    </div>
</form>

<script>
(function(){
    var body = document.getElementById('itemsBody');
    function fmt(n){ return (Math.round(n*100)/100).toFixed(2); }
    function recalc(){
        var subtotal = 0, tax = 0;
        body.querySelectorAll('tr.item-row').forEach(function(tr){
            var qty = parseFloat(tr.querySelector('.qty').value) || 0;
            var rate = parseFloat(tr.querySelector('.rate').value) || 0;
            var pct = parseFloat(tr.querySelector('.tax').value) || 0;
            var base = qty * rate;
            var lineTax = base * pct / 100;
            subtotal += base;
            tax += lineTax;
            tr.querySelector('.line-total').textContent = fmt(base + lineTax);
        });
        document.getElementById('subtotal').textContent = fmt(subtotal);
        document.getElementById('totalTax').textContent = fmt(tax);
        document.getElementById('grandTotal').textContent = fmt(subtotal + tax);
    }
    function reindex(){
        body.querySelectorAll('tr.item-row').forEach(function(tr, i){
            tr.querySelectorAll('input').forEach(function(inp){
                inp.name = inp.name.replace(/items\[\d+\]/, 'items[' + i + ']');
            });
        });
    }
    body.addEventListener('input', function(e){
        if (e.target.matches('.qty, .rate, .tax')) recalc();
    });
    body.addEventListener('click', function(e){
        var btn = e.target.closest('.btn-remove');
        if (!btn) return;
        if (body.querySelectorAll('tr.item-row').length <= 1) return;
        btn.closest('tr').remove();
        reindex();
        recalc();
    });
    document.getElementById('addLine').addEventListener('click', function(){
        var rows = body.querySelectorAll('tr.item-row');
        var clone = rows[rows.length - 1].cloneNode(true);
        clone.querySelectorAll('input').forEach(function(inp){
            if (inp.classList.contains('qty')) inp.value = 1;
            else if (inp.classList.contains('tax')) inp.value = 0;
            else inp.value = '';
        });
        body.appendChild(clone);
        reindex();
        recalc();
    });
    recalc();
})();
</script>
@endsection
