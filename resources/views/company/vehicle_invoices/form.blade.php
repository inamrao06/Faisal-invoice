@extends('layouts.app')
@section('title', $invoice->exists ? 'Edit Vehicle Invoice' : 'New Vehicle Invoice')
@section('page_title', $invoice->exists ? 'Edit Vehicle Invoice' : 'New Vehicle Invoice')

@push('styles')
    <style>
        .invoice-builder {
            max-width: 1280px;
            margin: 0 auto
        }

        .invoice-builder-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px
        }

        .invoice-builder-title {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .invoice-builder-title .icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: rgba(var(--bs-primary-rgb), .1);
            color: var(--bs-primary);
            font-size: 22px
        }

        .invoice-builder-title h5 {
            margin: 0;
            font-weight: 800
        }

        .invoice-builder-title p {
            margin: 2px 0 0;
            color: var(--bs-secondary-color);
            font-size: 13px
        }

        .invoice-builder-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 340px;
            gap: 18px;
            align-items: start
        }

        .invoice-panel {
            background: var(--bs-card-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04)
        }

        .invoice-panel+.invoice-panel {
            margin-top: 16px
        }

        .invoice-panel-head {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 18px;
            border-bottom: 1px solid var(--bs-border-color)
        }

        .invoice-panel-head .panel-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: color-mix(in srgb, var(--theme-active) 12%, transparent);
            color: var(--theme-active);
            font-size: 18px
        }

        .invoice-panel-head h6 {
            margin: 0;
            font-weight: 800
        }

        .invoice-panel-head p {
            margin: 1px 0 0;
            color: var(--bs-secondary-color);
            font-size: 12px
        }

        .invoice-panel-body {
            padding: 18px
        }

        .invoice-summary {
            position: sticky;
            top: 92px;
            overflow: hidden
        }

        .invoice-summary-top {
            padding: 20px;
            background: linear-gradient(135deg, var(--app-sidebar), #1F3A5F);
            color: #fff
        }

        .invoice-summary-top span {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .75
        }

        .invoice-summary-top strong {
            display: block;
            font-size: 28px;
            line-height: 1.15;
            margin-top: 4px
        }

        .invoice-summary-body {
            padding: 16px 18px
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid var(--bs-border-color);
            font-size: 13px
        }

        .summary-row:last-child {
            border-bottom: 0
        }

        .summary-row span {
            color: var(--bs-secondary-color)
        }

        .summary-row strong {
            font-variant-numeric: tabular-nums
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(var(--bs-primary-rgb), .1);
            color: var(--bs-primary);
            font-weight: 800
        }

        .pay-row {
            display: grid;
            grid-template-columns: minmax(150px, 1fr) minmax(170px, 1.1fr) minmax(150px, 1fr) minmax(130px, .9fr) 42px;
            gap: 10px;
            align-items: end;
            padding: 14px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-body-bg)
        }

        .pay-field label {
            display: block;
            margin-bottom: 6px;
            color: var(--bs-secondary-color);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase
        }

        .pay-field .form-control,
        .pay-field .form-select {
            width: 100%;
            min-height: 40px
        }

        .pay-remove {
            display: flex;
            justify-content: flex-end
        }

        .pay-remove .btn {
            width: 40px;
            height: 40px;
            display: inline-grid;
            place-items: center;
            padding: 0
        }

        .customer-tools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px
        }

        .customer-tools label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            padding: 7px 10px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-card-bg);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer
        }

        .customer-preview {
            margin-top: 10px;
            padding: 10px 12px;
            border: 1px dashed var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-body-bg);
            color: var(--bs-secondary-color);
            font-size: 12px;
            min-height: 46px
        }

        .new-customer-fields {
            display: none;
            margin-top: 10px
        }

        .inline-tools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px
        }

        .inline-tools label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin: 0;
            padding: 7px 10px;
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            background: var(--bs-card-bg);
            font-size: 12px;
            font-weight: 800;
            cursor: pointer
        }

        .new-vehicle-fields {
            display: none;
            margin-top: 10px
        }

        @media(max-width:991.98px) {
            .invoice-builder-grid {
                grid-template-columns: 1fr
            }

            .invoice-summary {
                position: static
            }

            .invoice-builder-head {
                align-items: flex-start;
                flex-direction: column
            }

            .invoice-builder-actions {
                width: 100%;
                display: flex
            }

            .invoice-builder-actions .btn {
                flex: 1
            }
        }

        @media(max-width:767.98px) {
            .pay-row {
                grid-template-columns: 1fr
            }

            .pay-remove {
                justify-content: flex-start
            }
        }
    </style>
@endpush

@section('content')
    <form class="invoice-builder" method="POST"
        action="{{ $invoice->exists ? route('company.vehicle-invoices.update', $invoice) : route('company.vehicle-invoices.store') }}">
        @csrf
        @if ($invoice->exists)
            @method('PUT')
        @endif

        <div class="invoice-builder-head">
            <div class="invoice-builder-title">
                <div class="icon"><i class="ti ti-file-invoice"></i></div>
                <div>
                    <h5>{{ $invoice->exists ? 'Edit Vehicle Invoice' : 'New Vehicle Invoice' }}</h5>
                    <p>Build the sale, warranty, and payment record in one focused layout.</p>
                </div>
            </div>
            <div class="invoice-builder-actions d-flex gap-2">
                <a class="btn btn-light" href="{{ route('company.vehicle-invoices.index') }}">Cancel</a>
                <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Generate Invoice</button>
            </div>
        </div>

        <div class="invoice-builder-grid">
            <div>
                <section class="invoice-panel">
                    <div class="invoice-panel-head">
                        <div class="panel-icon"><i class="ti ti-car"></i></div>
                        <div>
                            <h6>Customer, Vehicle &amp; Sale</h6>
                            <p>Select the seller, buyer, stock vehicle, category, dates, and price.</p>
                        </div>
                    </div>
                    <div class="invoice-panel-body">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label" for="customer_id">Buyer Customer <span
                                        class="text-danger">*</span></label>
                                @php $buyerMode = old('buyer_customer_mode', 'existing'); @endphp
                                <div class="customer-tools" data-mode-group="buyer">
                                    <label><input type="radio" name="buyer_customer_mode" value="existing"
                                            @checked($buyerMode === 'existing')> Existing</label>
                                    <label><input type="radio" name="buyer_customer_mode" value="new"
                                            @checked($buyerMode === 'new')> New customer</label>
                                </div>
                                <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                    name="customer_id" required>
                                    <option value="">Select buyer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-email="{{ $customer->email }}"
                                            data-phone="{{ $customer->phone }}"
                                            data-address="{{ trim($customer->address . ' ' . $customer->postcode) }}"
                                            @selected(old('customer_id', $invoice->customer_id) == $customer->id)>
                                            {{ $customer->name }} {{ $customer->phone ? ' - ' . $customer->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="customer-preview" id="buyerPreview">Choose an existing customer to show details.
                                </div>
                                <div class="new-customer-fields" id="buyerNewFields">
                                    <div class="row g-2">
                                        <div class="col-12"><input
                                                class="form-control @error('buyer_name') is-invalid @enderror"
                                                name="buyer_name" value="{{ old('buyer_name') }}" placeholder="Buyer name">
                                        </div>
                                        <div class="col-md-6"><input class="form-control" type="email" name="buyer_email"
                                                value="{{ old('buyer_email') }}" placeholder="Email"></div>
                                        <div class="col-md-6"><input class="form-control" name="buyer_phone"
                                                value="{{ old('buyer_phone') }}" placeholder="Phone"></div>
                                        <div class="col-md-8"><input class="form-control" name="buyer_address"
                                                value="{{ old('buyer_address') }}" placeholder="Address"></div>
                                        <div class="col-md-4"><input class="form-control" name="buyer_postcode"
                                                value="{{ old('buyer_postcode') }}" placeholder="Postcode"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6" id="sellerCustomerWrap">
                                <label class="form-label" for="seller_customer_id">Seller Customer <span
                                        class="text-danger">*</span></label>
                                @php $sellerMode = old('seller_customer_mode', 'existing'); @endphp
                                <div class="customer-tools" data-mode-group="seller">
                                    <label><input type="radio" name="seller_customer_mode" value="existing"
                                            @checked($sellerMode === 'existing')> Existing</label>
                                    <label><input type="radio" name="seller_customer_mode" value="new"
                                            @checked($sellerMode === 'new')> New customer</label>
                                </div>
                                <select class="form-select @error('seller_customer_id') is-invalid @enderror"
                                    id="seller_customer_id" name="seller_customer_id">
                                    <option value="">Select seller</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" data-email="{{ $customer->email }}"
                                            data-phone="{{ $customer->phone }}"
                                            data-address="{{ trim($customer->address . ' ' . $customer->postcode) }}"
                                            @selected(old('seller_customer_id', $invoice->seller_customer_id) == $customer->id)>
                                            {{ $customer->name }} {{ $customer->phone ? ' - ' . $customer->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('seller_customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="customer-preview" id="sellerPreview">Choose an existing customer to show
                                    details.</div>
                                <div class="new-customer-fields" id="sellerNewFields">
                                    <div class="row g-2">
                                        <div class="col-12"><input
                                                class="form-control @error('seller_name') is-invalid @enderror"
                                                name="seller_name" value="{{ old('seller_name') }}"
                                                placeholder="Seller name"></div>
                                        <div class="col-md-6"><input class="form-control" type="email"
                                                name="seller_email" value="{{ old('seller_email') }}"
                                                placeholder="Email"></div>
                                        <div class="col-md-6"><input class="form-control" name="seller_phone"
                                                value="{{ old('seller_phone') }}" placeholder="Phone"></div>
                                        <div class="col-md-8"><input class="form-control" name="seller_address"
                                                value="{{ old('seller_address') }}" placeholder="Address"></div>
                                        <div class="col-md-4"><input class="form-control" name="seller_postcode"
                                                value="{{ old('seller_postcode') }}" placeholder="Postcode"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="vehicle_id">Vehicle <span
                                        class="text-danger">*</span></label>
                                @php $vehicleMode = old('vehicle_mode', 'existing'); @endphp
                                <div class="inline-tools">
                                    <label><input type="radio" name="vehicle_mode" value="existing"
                                            @checked($vehicleMode === 'existing')> Existing car</label>
                                    <label><input type="radio" name="vehicle_mode" value="new"
                                            @checked($vehicleMode === 'new')> Add new car</label>
                                </div>
                                <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id"
                                    name="vehicle_id" required>
                                    <option value="">Select vehicle</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-price="{{ $vehicle->sale_price }}"
                                            data-category="{{ $vehicle->vehicle_category_id }}"
                                            data-reg="{{ $vehicle->registration_no }}" data-vin="{{ $vehicle->vin }}"
                                            data-year="{{ $vehicle->year }}" data-mileage="{{ $vehicle->mileage }}"
                                            data-keys="{{ $vehicle->keys_count }}" @selected(old('vehicle_id', $invoice->vehicle_id) == $vehicle->id)>
                                            {{ $vehicle->make_model }}
                                            {{ $vehicle->registration_no ? ' - ' . $vehicle->registration_no : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="customer-preview" id="vehiclePreview">Choose an existing car to show details.
                                </div>
                                <div class="new-vehicle-fields" id="newVehicleFields">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <input
                                                class="form-control @error('new_vehicle_make_model') is-invalid @enderror"
                                                name="new_vehicle_make_model" value="{{ old('new_vehicle_make_model') }}"
                                                placeholder="Make / model">
                                            @error('new_vehicle_make_model')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6"><input class="form-control"
                                                name="new_vehicle_registration_no"
                                                value="{{ old('new_vehicle_registration_no') }}"
                                                placeholder="Registration"></div>
                                        <div class="col-md-6"><input class="form-control" name="new_vehicle_vin"
                                                value="{{ old('new_vehicle_vin') }}" placeholder="VIN / Chassis"></div>
                                        <div class="col-md-4"><input class="form-control" type="number"
                                                name="new_vehicle_year" value="{{ old('new_vehicle_year') }}"
                                                placeholder="Year"></div>
                                        <div class="col-md-4"><input class="form-control" type="number"
                                                name="new_vehicle_mileage" value="{{ old('new_vehicle_mileage') }}"
                                                placeholder="Mileage"></div>
                                        <div class="col-md-4"><input class="form-control" type="number"
                                                name="new_vehicle_keys_count" value="{{ old('new_vehicle_keys_count') }}"
                                                placeholder="Keys"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="vehicle_category_id">Vehicle Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('vehicle_category_id') is-invalid @enderror"
                                    id="vehicle_category_id" name="vehicle_category_id" required>
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            data-warranty="{{ $category->default_warranty_provider_id }}"
                                            data-months="{{ $category->default_warranty_months }}"
                                            @selected(old('vehicle_category_id', $invoice->vehicle_category_id) == $category->id)>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('vehicle_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="transaction_type">Sale Type</label>
                                <select class="form-select @error('transaction_type') is-invalid @enderror"
                                    id="transaction_type" name="transaction_type">
                                    @foreach (['vehicle_sale' => 'My Car Sale', 'customer_to_customer' => 'Customer to Customer Sale', 'vehicle_reservation' => 'Vehicle Reservation / Deposit', 'part_payment' => 'Part Payment', 'final_payment' => 'Final Payment'] as $v => $l)
                                        <option value="{{ $v }}" @selected(old('transaction_type', $invoice->transaction_type) === $v)>
                                            {{ $l }}</option>
                                    @endforeach
                                </select>
                                @error('transaction_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="invoice_date">Invoice Date <span
                                        class="text-danger">*</span></label>
                                <input class="form-control @error('invoice_date') is-invalid @enderror" id="invoice_date"
                                    type="date" name="invoice_date"
                                    value="{{ old('invoice_date', $invoice->invoice_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                    required>
                                @error('invoice_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="sale_date">Sale Date</label>
                                <input class="form-control @error('sale_date') is-invalid @enderror" id="sale_date"
                                    type="date" name="sale_date"
                                    value="{{ old('sale_date', $invoice->sale_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                                @error('sale_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="vehicle_price">Vehicle Price <span
                                        class="text-danger">*</span></label>
                                <input class="form-control calc @error('vehicle_price') is-invalid @enderror"
                                    id="vehicle_price" type="number" step="0.01" name="vehicle_price"
                                    value="{{ old('vehicle_price', $invoice->vehicle_price) }}" required>
                                @error('vehicle_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="discount">Discount</label>
                                <input class="form-control calc @error('discount') is-invalid @enderror" id="discount"
                                    type="number" step="0.01" name="discount"
                                    value="{{ old('discount', $invoice->discount ?? 0) }}">
                                @error('discount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="commission_amount">Commission</label>
                                <input class="form-control calc @error('commission_amount') is-invalid @enderror"
                                    id="commission_amount" type="number" step="0.01" name="commission_amount"
                                    value="{{ old('commission_amount', $invoice->commission_amount ?? 0) }}">
                                @error('commission_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="total_sale_price">Total Sale Price</label>
                                <input class="form-control fw-bold" id="total_sale_price" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="commission_notes">Commission Notes</label>
                                <input class="form-control @error('commission_notes') is-invalid @enderror"
                                    id="commission_notes" name="commission_notes"
                                    value="{{ old('commission_notes', $invoice->commission_notes) }}"
                                    placeholder="Optional commission setup">
                                @error('commission_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="invoice-panel">
                    <div class="invoice-panel-head">
                        <div class="panel-icon"><i class="ti ti-shield-check"></i></div>
                        <div>
                            <h6>Warranty</h6>
                            <p>Attach an optional provider and duration for the sale.</p>
                        </div>
                    </div>
                    <div class="invoice-panel-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="warranty_provider_id">Warranty Provider</label>
                                <select class="form-select @error('warranty_provider_id') is-invalid @enderror"
                                    id="warranty_provider_id" name="warranty_provider_id">
                                    <option value="">No Warranty</option>
                                    @foreach ($warrantyProviders as $provider)
                                        <option value="{{ $provider->id }}" @selected(old('warranty_provider_id', $invoice->warranty_provider_id) == $provider->id)>
                                            {{ $provider->name }}</option>
                                    @endforeach
                                </select>
                                @error('warranty_provider_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="warranty_duration_months">Warranty Duration</label>
                                <select class="form-select @error('warranty_duration_months') is-invalid @enderror"
                                    id="warranty_duration_months" name="warranty_duration_months">
                                    <option value="">Select months</option>
                                    @foreach ($warrantyDurations as $duration)
                                        <option value="{{ $duration->months }}" @selected((string) old('warranty_duration_months', $invoice->warranty_duration_months) === (string) $duration->months)>
                                            {{ $duration->name }}</option>
                                    @endforeach
                                </select>
                                @error('warranty_duration_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="invoice-panel">
                    <div class="invoice-panel-head justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="panel-icon"><i class="ti ti-cash"></i></div>
                            <div>
                                <h6>Payments</h6>
                                <p>Record deposits and payments received for this sale.</p>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-sm" type="button" id="addPayment"><i
                                class="ti ti-plus me-1"></i>Add Payment</button>
                    </div>
                    <div class="invoice-panel-body">
                        <div id="payments">
                            @php $oldPayments=old('payments',$invoice->exists?$invoice->payments->map(fn($p)=>['payment_date'=>$p->payment_date?->format('Y-m-d'),'payment_type'=>$p->payment_type,'method'=>$p->method,'amount'=>$p->amount,'notes'=>$p->notes])->toArray():[['payment_date'=>now()->format('Y-m-d'),'payment_type'=>'deposit','method'=>'Bank Transfer','amount'=>'','notes'=>'']]); @endphp
                            @foreach ($oldPayments as $i => $p)
                                <div class="pay-row mb-2">
                                    <div class="pay-field">
                                        <label>Payment Date</label>
                                        <input class="form-control" type="date"
                                            name="payments[{{ $i }}][payment_date]"
                                            value="{{ $p['payment_date'] ?? now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="pay-field">
                                        <label>Payment Type</label>
                                        <select class="form-select" name="payments[{{ $i }}][payment_type]">
                                            @foreach (['deposit' => 'Deposit', 'part_payment' => 'Part Payment', 'further_payment' => 'Further Payment', 'final_payment' => 'Final Payment'] as $v => $l)
                                                <option value="{{ $v }}" @selected(($p['payment_type'] ?? 'deposit') === $v)>
                                                    {{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="pay-field">
                                        <label>Method</label>
                                        <select class="form-select" name="payments[{{ $i }}][method]">
                                            @foreach ($paymentMethods as $method)
                                                <option value="{{ $method->name }}" @selected(($p['method'] ?? '') === $method->name)>
                                                    {{ $method->name }}</option>
                                            @endforeach
                                            <option value="Cash" @selected(($p['method'] ?? '') === 'Cash')>Cash</option>
                                            <option value="Bank Transfer" @selected(($p['method'] ?? '') === 'Bank Transfer')>Bank Transfer
                                            </option>
                                            <option value="Card" @selected(($p['method'] ?? '') === 'Card')>Card</option>
                                            <option value="Other" @selected(($p['method'] ?? '') === 'Other')>Other</option>
                                        </select>
                                    </div>
                                    <div class="pay-field">
                                        <label>Amount</label>
                                        <input class="form-control payment-amount" type="number" step="0.01"
                                            name="payments[{{ $i }}][amount]"
                                            value="{{ $p['amount'] ?? '' }}" placeholder="0.00">
                                    </div>
                                    <div class="pay-remove">
                                        <button class="btn btn-soft-danger btn-sm remove-payment" type="button"
                                            title="Remove"><i class="ti ti-trash"></i></button>
                                    </div>
                                    <input type="hidden" name="payments[{{ $i }}][notes]"
                                        value="{{ $p['notes'] ?? '' }}">
                                </div>
                            @endforeach
                            <div class="totals d-none"></div>
                        </div>
                    </div>
                </section>

                <section class="invoice-panel">
                    <div class="invoice-panel-head">
                        <div class="panel-icon"><i class="ti ti-notes"></i></div>
                        <div>
                            <h6>Notes</h6>
                            <p>Optional internal or customer-facing notes for this invoice.</p>
                        </div>
                    </div>
                    <div class="invoice-panel-body">
                        <label class="form-label" for="notes">Invoice Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4">{{ old('notes', $invoice->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </section>
            </div>

            <aside class="invoice-panel invoice-summary">
                <div class="invoice-summary-top">
                    <span>Total Sale Price</span>
                    <strong id="summaryTotal">0.00</strong>
                </div>
                <div class="invoice-summary-body">
                    <div class="summary-row"><span>Total Paid</span><strong id="paidTotal">0.00</strong></div>
                    <div class="summary-row"><span>Balance</span><strong id="balanceTotal">0.00</strong></div>
                    <div class="summary-row align-items-center"><span>Status</span><strong class="status-pill"
                            id="paymentStatus">Outstanding</strong></div>
                    <button class="btn btn-primary w-100 mt-3" type="submit"><i
                            class="ti ti-device-floppy me-1"></i>Save Invoice</button>
                </div>
            </aside>
        </div>
    </form>

    <script>
        (function() {
            var payments = document.getElementById('payments');
            var price = document.getElementById('vehicle_price');
            var discount = document.getElementById('discount');
            var commission = document.getElementById('commission_amount');
            var total = document.getElementById('total_sale_price');
            var summaryTotal = document.getElementById('summaryTotal');
            var transactionType = document.getElementById('transaction_type');
            var sellerWrap = document.getElementById('sellerCustomerWrap');
            var seller = document.getElementById('seller_customer_id');
            var vehicle = document.getElementById('vehicle_id');
            var newVehicleFields = document.getElementById('newVehicleFields');
            var vehiclePreview = document.getElementById('vehiclePreview');

            function n(v) {
                return parseFloat(v) || 0;
            }

            function f(v) {
                return (Math.round(v * 100) / 100).toFixed(2);
            }

            function calc() {
                var due = Math.max(0, n(price.value) - n(discount.value) + n(commission.value));
                total.value = f(due);
                summaryTotal.textContent = f(due);
                var paid = 0;
                payments.querySelectorAll('.payment-amount').forEach(function(input) {
                    paid += n(input.value);
                });
                document.getElementById('paidTotal').textContent = f(paid);
                document.getElementById('balanceTotal').textContent = f(Math.max(0, due - paid));
                document.getElementById('paymentStatus').textContent = paid >= due && due > 0 ? 'Paid' : (paid > 0 ?
                    'Part Paid' : 'Outstanding');
            }

            function selectedMode(role) {
                var selected = document.querySelector('input[name="' + role + '_customer_mode"]:checked');
                return selected ? selected.value : 'existing';
            }

            function previewCustomer(role) {
                var select = document.getElementById(role === 'buyer' ? 'customer_id' : 'seller_customer_id');
                var preview = document.getElementById(role + 'Preview');
                var option = select && select.selectedOptions ? select.selectedOptions[0] : null;
                if (!preview) return;
                if (!option || !option.value) {
                    preview.textContent = 'Choose an existing customer to show details.';
                    return;
                }
                preview.innerHTML = [
                    '<strong>' + option.textContent.trim() + '</strong>',
                    option.dataset.email || '',
                    option.dataset.phone || '',
                    option.dataset.address || ''
                ].filter(Boolean).join('<br>');
            }

            function toggleCustomerMode(role) {
                var mode = selectedMode(role);
                var select = document.getElementById(role === 'buyer' ? 'customer_id' : 'seller_customer_id');
                var fields = document.getElementById(role + 'NewFields');
                var preview = document.getElementById(role + 'Preview');
                if (!select || !fields) return;
                select.required = mode === 'existing' && (role === 'buyer' || transactionType.value ===
                    'customer_to_customer');
                select.disabled = mode === 'new';
                fields.style.display = mode === 'new' ? 'block' : 'none';
                fields.querySelectorAll('input').forEach(function(input) {
                    input.disabled = mode !== 'new';
                });
                var nameInput = fields.querySelector('input[name="' + role + '_name"]');
                if (nameInput) nameInput.required = mode === 'new' && (role === 'buyer' || transactionType.value ===
                    'customer_to_customer');
                if (preview) preview.style.display = mode === 'existing' ? 'block' : 'none';
                previewCustomer(role);
            }

            function toggleSeller() {
                var show = transactionType && transactionType.value === 'customer_to_customer';
                sellerWrap.style.display = show ? 'block' : 'none';
                seller.required = show && selectedMode('seller') === 'existing';
                if (!show) seller.value = '';
                toggleCustomerMode('buyer');
                toggleCustomerMode('seller');
            }

            function selectedVehicleMode() {
                var selected = document.querySelector('input[name="vehicle_mode"]:checked');
                return selected ? selected.value : 'existing';
            }

            function previewVehicle() {
                var option = vehicle && vehicle.selectedOptions ? vehicle.selectedOptions[0] : null;
                if (!vehiclePreview) return;
                if (!option || !option.value) {
                    vehiclePreview.textContent = 'Choose an existing car to show details.';
                    return;
                }
                vehiclePreview.innerHTML = [
                    '<strong>' + option.textContent.trim() + '</strong>',
                    option.dataset.reg ? 'Reg: ' + option.dataset.reg : '',
                    option.dataset.vin ? 'VIN: ' + option.dataset.vin : '',
                    option.dataset.year ? 'Year: ' + option.dataset.year : '',
                    option.dataset.mileage ? 'Mileage: ' + option.dataset.mileage : '',
                    option.dataset.keys ? 'Keys: ' + option.dataset.keys : '',
                    option.dataset.price ? 'Price: ' + option.dataset.price : ''
                ].filter(Boolean).join('<br>');
            }

            function toggleVehicleMode() {
                var mode = selectedVehicleMode();
                vehicle.required = mode === 'existing';
                vehicle.disabled = mode === 'new';
                if (vehiclePreview) vehiclePreview.style.display = mode === 'existing' ? 'block' : 'none';
                if (newVehicleFields) {
                    newVehicleFields.style.display = mode === 'new' ? 'block' : 'none';
                    newVehicleFields.querySelectorAll('input').forEach(function(input) {
                        input.disabled = mode !== 'new';
                    });
                    var makeModel = newVehicleFields.querySelector('input[name="new_vehicle_make_model"]');
                    if (makeModel) makeModel.required = mode === 'new';
                }
                previewVehicle();
            }

            document.addEventListener('input', function(e) {
                if (e.target.matches('.calc,.payment-amount')) calc();
            });
            document.querySelectorAll('input[name="buyer_customer_mode"],input[name="seller_customer_mode"]').forEach(
                function(input) {
                    input.addEventListener('change', function() {
                        toggleCustomerMode(input.name.indexOf('buyer') === 0 ? 'buyer' : 'seller');
                    });
                });
            document.querySelectorAll('input[name="vehicle_mode"]').forEach(function(input) {
                input.addEventListener('change', toggleVehicleMode);
            });
            document.getElementById('customer_id').addEventListener('change', function() {
                previewCustomer('buyer');
            });
            document.getElementById('seller_customer_id').addEventListener('change', function() {
                previewCustomer('seller');
            });
            transactionType.addEventListener('change', toggleSeller);
            vehicle.addEventListener('change', function() {
                var option = this.selectedOptions[0];
                if (option) {
                    if (!price.value || price.value === '0.00') price.value = option.dataset.price || '';
                    if (option.dataset.category) document.getElementById('vehicle_category_id').value = option
                        .dataset.category;
                }
                previewVehicle();
                calc();
            });
            document.getElementById('vehicle_category_id').addEventListener('change', function() {
                var option = this.selectedOptions[0];
                if (option) {
                    if (option.dataset.warranty) document.getElementById('warranty_provider_id').value = option
                        .dataset.warranty;
                    if (option.dataset.months) document.getElementById('warranty_duration_months').value =
                        option.dataset.months;
                }
            });
            document.getElementById('addPayment').addEventListener('click', function() {
                var row = payments.querySelector('.pay-row').cloneNode(true);
                row.querySelectorAll('input').forEach(function(input) {
                    input.value = input.type === 'date' ? new Date().toISOString().slice(0, 10) : '';
                });
                payments.insertBefore(row, payments.querySelector('.totals'));
                reindex();
                calc();
            });
            payments.addEventListener('click', function(e) {
                var button = e.target.closest('.remove-payment');
                if (!button) return;
                if (payments.querySelectorAll('.pay-row').length > 1) button.closest('.pay-row').remove();
                reindex();
                calc();
            });

            function reindex() {
                payments.querySelectorAll('.pay-row').forEach(function(row, index) {
                    row.querySelectorAll('input,select').forEach(function(input) {
                        input.name = input.name.replace(/payments\[\d+\]/, 'payments[' + index + ']');
                    });
                });
            }
            toggleVehicleMode();
            toggleSeller();
            calc();
        })();
    </script>
@endsection
