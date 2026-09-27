@extends('layouts.app')
@section('title',$invoice->exists?'Edit Vehicle Invoice':'New Vehicle Invoice')
@section('page_title',$invoice->exists?'Edit Vehicle Invoice':'New Vehicle Invoice')
@section('content')
<div class="row">
    <div class="col-12">
        <form class="card form-card mb-0" method="POST"
              action="{{ $invoice->exists ? route('company.vehicle-invoices.update',$invoice) : route('company.vehicle-invoices.store') }}">
            @csrf
            @if($invoice->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-file-invoice"></i></div>
                    <div>
                        <h6>{{ $invoice->exists ? 'Edit Vehicle Invoice' : 'New Vehicle Invoice' }}</h6>
                        <p>Customer, vehicle, payment and warranty details.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Customer, Vehicle & Sale --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-car"></i></div>
                    <div>
                        <h6>Customer, Vehicle &amp; Sale</h6>
                        <p>Select the customer and vehicle for this invoice.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="customer_id">Customer <span class="text-danger">*</span></label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                            <option value="">Select customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" @selected(old('customer_id',$invoice->customer_id)==$customer->id)>
                                    {{ $customer->name }} {{ $customer->phone ? ' - '.$customer->phone : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="vehicle_id">Vehicle <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id" required>
                            <option value="">Select vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" data-price="{{ $vehicle->sale_price }}" data-category="{{ $vehicle->vehicle_category_id }}" @selected(old('vehicle_id',$invoice->vehicle_id)==$vehicle->id)>
                                    {{ $vehicle->make_model }} {{ $vehicle->registration_no ? ' - '.$vehicle->registration_no : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="vehicle_category_id">Vehicle Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('vehicle_category_id') is-invalid @enderror" id="vehicle_category_id" name="vehicle_category_id" required>
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-warranty="{{ $category->default_warranty_provider_id }}" data-months="{{ $category->default_warranty_months }}" @selected(old('vehicle_category_id',$invoice->vehicle_category_id)==$category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="transaction_type">Transaction Type</label>
                        <select class="form-select @error('transaction_type') is-invalid @enderror" id="transaction_type" name="transaction_type">
                            @foreach(['vehicle_sale'=>'Vehicle Sale','vehicle_reservation'=>'Vehicle Reservation / Deposit','part_payment'=>'Part Payment','final_payment'=>'Final Payment'] as $v=>$l)
                                <option value="{{ $v }}" @selected(old('transaction_type',$invoice->transaction_type)===$v)>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('transaction_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="invoice_date">Invoice Date <span class="text-danger">*</span></label>
                        <input class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date" type="date" name="invoice_date"
                               value="{{ old('invoice_date',$invoice->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                        @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="sale_date">Sale Date</label>
                        <input class="form-control @error('sale_date') is-invalid @enderror" id="sale_date" type="date" name="sale_date"
                               value="{{ old('sale_date',$invoice->sale_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                        @error('sale_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="vehicle_price">Vehicle Price <span class="text-danger">*</span></label>
                        <input class="form-control calc @error('vehicle_price') is-invalid @enderror" id="vehicle_price" type="number" step="0.01" name="vehicle_price"
                               value="{{ old('vehicle_price',$invoice->vehicle_price) }}" required>
                        @error('vehicle_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="discount">Discount</label>
                        <input class="form-control calc @error('discount') is-invalid @enderror" id="discount" type="number" step="0.01" name="discount"
                               value="{{ old('discount',$invoice->discount ?? 0) }}">
                        @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="total_sale_price">Total Sale Price</label>
                        <input class="form-control" id="total_sale_price" readonly>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Warranty --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-shield-check"></i></div>
                    <div>
                        <h6>Warranty</h6>
                        <p>Optional warranty cover for this sale.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="warranty_provider_id">Warranty Provider</label>
                        <select class="form-select @error('warranty_provider_id') is-invalid @enderror" id="warranty_provider_id" name="warranty_provider_id">
                            <option value="">No Warranty</option>
                            @foreach($warrantyProviders as $provider)
                                <option value="{{ $provider->id }}" @selected(old('warranty_provider_id',$invoice->warranty_provider_id)==$provider->id)>{{ $provider->name }}</option>
                            @endforeach
                        </select>
                        @error('warranty_provider_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="warranty_duration_months">Warranty Duration</label>
                        <select class="form-select @error('warranty_duration_months') is-invalid @enderror" id="warranty_duration_months" name="warranty_duration_months">
                            <option value="">Select months</option>
                            @foreach($warrantyDurations as $duration)
                                <option value="{{ $duration->months }}" @selected((string)old('warranty_duration_months',$invoice->warranty_duration_months)===(string)$duration->months)>{{ $duration->name }}</option>
                            @endforeach
                        </select>
                        @error('warranty_duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Payments --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-cash"></i></div>
                    <div>
                        <h6>Payments</h6>
                        <p>Record deposits and payments received for this sale.</p>
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-primary btn-sm" type="button" id="addPayment">
                        <i class="ti ti-plus me-1"></i>Add Payment
                    </button>
                </div>
                <div id="payments">
                    @php $oldPayments=old('payments',$invoice->exists?$invoice->payments->map(fn($p)=>['payment_date'=>$p->payment_date?->format('Y-m-d'),'payment_type'=>$p->payment_type,'method'=>$p->method,'amount'=>$p->amount,'notes'=>$p->notes])->toArray():[['payment_date'=>now()->format('Y-m-d'),'payment_type'=>'deposit','method'=>'Bank Transfer','amount'=>'','notes'=>'']]); @endphp
                    @foreach($oldPayments as $i=>$p)
                        <div class="pay-row row g-2 align-items-center mb-2">
                            <div class="col-md-3">
                                <input class="form-control" type="date" name="payments[{{ $i }}][payment_date]"
                                       value="{{ $p['payment_date'] ?? now()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="payments[{{ $i }}][payment_type]">
                                    @foreach(['deposit'=>'Deposit','part_payment'=>'Part Payment','further_payment'=>'Further Payment','final_payment'=>'Final Payment'] as $v=>$l)
                                        <option value="{{ $v }}" @selected(($p['payment_type']??'deposit')===$v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="payments[{{ $i }}][method]">
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->name }}" @selected(($p['method']??'')===$method->name)>{{ $method->name }}</option>
                                    @endforeach
                                    <option value="Cash" @selected(($p['method']??'')==='Cash')>Cash</option>
                                    <option value="Bank Transfer" @selected(($p['method']??'')==='Bank Transfer')>Bank Transfer</option>
                                    <option value="Card" @selected(($p['method']??'')==='Card')>Card</option>
                                    <option value="Other" @selected(($p['method']??'')==='Other')>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input class="form-control payment-amount" type="number" step="0.01" name="payments[{{ $i }}][amount]"
                                       value="{{ $p['amount'] ?? '' }}" placeholder="Amount">
                            </div>
                            <div class="col-md-1 text-end">
                                <button class="btn btn-soft-danger btn-sm remove-payment" type="button" title="Remove">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                            <input type="hidden" name="payments[{{ $i }}][notes]" value="{{ $p['notes'] ?? '' }}">
                        </div>
                    @endforeach
                    <div class="totals d-flex flex-wrap justify-content-end gap-4 border-top pt-3 mt-3 text-end">
                        <div>
                            <span class="text-muted fs-12 text-uppercase d-block">Total Paid</span>
                            <strong class="fs-16" id="paidTotal">0.00</strong>
                        </div>
                        <div>
                            <span class="text-muted fs-12 text-uppercase d-block">Balance</span>
                            <strong class="fs-16" id="balanceTotal">0.00</strong>
                        </div>
                        <div class="grand">
                            <span class="text-muted fs-12 text-uppercase d-block">Status</span>
                            <strong class="fs-16" id="paymentStatus">Outstanding</strong>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Notes --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-notes"></i></div>
                    <div>
                        <h6>Notes</h6>
                        <p>Optional notes for this invoice.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="notes">Invoice Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes',$invoice->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>Generate Invoice
                </button>
                <a class="btn btn-light" href="{{ route('company.vehicle-invoices.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
(function(){var payments=document.getElementById('payments'), price=document.getElementById('vehicle_price'), discount=document.getElementById('discount'), total=document.getElementById('total_sale_price');function n(v){return parseFloat(v)||0}function f(v){return (Math.round(v*100)/100).toFixed(2)}function calc(){var due=Math.max(0,n(price.value)-n(discount.value));total.value=f(due);var paid=0;payments.querySelectorAll('.payment-amount').forEach(i=>paid+=n(i.value));document.getElementById('paidTotal').textContent=f(paid);document.getElementById('balanceTotal').textContent=f(Math.max(0,due-paid));document.getElementById('paymentStatus').textContent=paid>=due&&due>0?'Paid':(paid>0?'Part Paid':'Outstanding')}document.addEventListener('input',e=>{if(e.target.matches('.calc,.payment-amount'))calc()});document.getElementById('vehicle_id').addEventListener('change',function(){var o=this.selectedOptions[0]; if(o){ if(!price.value || price.value==='0.00') price.value=o.dataset.price||''; var cat=o.dataset.category; if(cat) document.getElementById('vehicle_category_id').value=cat; } calc();});document.getElementById('vehicle_category_id').addEventListener('change',function(){var o=this.selectedOptions[0]; if(o){ if(o.dataset.warranty) document.getElementById('warranty_provider_id').value=o.dataset.warranty; if(o.dataset.months) document.getElementById('warranty_duration_months').value=o.dataset.months; }});document.getElementById('addPayment').addEventListener('click',function(){var row=payments.querySelector('.pay-row').cloneNode(true);row.querySelectorAll('input').forEach(i=>{if(i.type==='date') i.value=new Date().toISOString().slice(0,10); else i.value=''});payments.insertBefore(row,payments.querySelector('.totals'));reindex();calc();});payments.addEventListener('click',e=>{var b=e.target.closest('.remove-payment'); if(!b)return; if(payments.querySelectorAll('.pay-row').length>1)b.closest('.pay-row').remove(); reindex(); calc();});function reindex(){payments.querySelectorAll('.pay-row').forEach((r,i)=>r.querySelectorAll('input,select').forEach(el=>el.name=el.name.replace(/payments\[\d+\]/,'payments['+i+']')))}calc();})();
</script>
@endsection
