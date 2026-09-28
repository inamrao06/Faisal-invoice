@extends('layouts.app')
@section('title',$invoice->invoice_no)
@section('page_title','Vehicle Sales Invoice')
@section('content')
@php
    $company = $invoice->company;
    $currency = $company?->currency?->symbol ?? 'PKR';
    $customTemplate = \App\Models\CompanyDocumentTemplate::defaultFor($invoice->branch_id, 'invoice');
@endphp

<div class="invoice-page">
    <div class="invoice-toolbar">
        <div>
            <h2>Vehicle Sales Invoice</h2>
            <p>Company-branded customer invoice with vehicle, warranty, totals, and payments.</p>
        </div>
        <div class="invoice-actions">
            <button type="button" class="btn btn-soft-secondary btn-sm" onclick="window.print()"><i class="ti ti-printer me-1"></i>Print</button>
            <a class="btn btn-light btn-sm" href="{{ route('company.vehicle-invoices.index') }}">Back</a>
            <a class="btn btn-primary btn-sm" href="{{ route('company.vehicle-invoices.edit',$invoice) }}"><i class="ti ti-pencil me-1"></i>Edit</a>
        </div>
    </div>

    @if($customTemplate)
        {!! $customTemplate->render(\App\Models\CompanyDocumentTemplate::invoiceValues($invoice)) !!}
    @else
    <div class="invoice-sheet">
        <div class="invoice-hero">
            <div class="brand-row">
                <div class="brand-mark">
                    @if($company?->logo_path)
                        <img src="{{ asset('storage/'.$company->logo_path) }}" alt="{{ $company->name }}">
                    @else
                        {{ strtoupper(substr($company?->name ?? 'C', 0, 1)) }}
                    @endif
                </div>
                <div class="brand-copy">
                    <h3>{{ $company?->name ?? 'Company' }}</h3>
                    @if($company?->company_email)<p><i class="ti ti-mail me-1"></i>{{ $company->company_email }}</p>@endif
                    @if($company?->phone)<p><i class="ti ti-phone me-1"></i>{{ $company->phone }}</p>@endif
                    @if($company?->website)<p><i class="ti ti-world me-1"></i>{{ $company->website }}</p>@endif
                    @if($company?->address || $company?->city)<p><i class="ti ti-map-pin me-1"></i>{{ trim(($company->address ?? '') . ' ' . ($company->city ?? '')) }}</p>@endif
                    @if($company?->tax_number)<p><i class="ti ti-file-certificate me-1"></i>Tax No: {{ $company->tax_number }}</p>@endif
                </div>
            </div>
            <div class="invoice-title">
                <span>Invoice No</span>
                <strong>{{ $invoice->invoice_no }}</strong>
                <span class="status-pill"><i class="ti ti-circle-check"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}</span>
            </div>
        </div>

        <div class="invoice-meta-grid">
            <div class="meta-item"><span>Invoice Date</span><strong>{{ $invoice->invoice_date->format('d M Y') }}</strong></div>
            <div class="meta-item"><span>Sale Date</span><strong>{{ $invoice->sale_date?->format('d M Y') ?? '-' }}</strong></div>
            <div class="meta-item"><span>Sale Type</span><strong>{{ str($invoice->transaction_type)->replace('_',' ')->title() }}</strong></div>
            <div class="meta-item"><span>Balance</span><strong>{{ $currency }} {{ number_format($invoice->balance_amount,2) }}</strong></div>
        </div>

        <div class="invoice-body">
            <div class="row g-3">
                @if($invoice->transaction_type === 'customer_to_customer' && $invoice->sellerCustomer)
                    <div class="col-md-4">
                        <div class="info-strip h-100">
                            <p class="text-muted fs-12 text-uppercase mb-1">Seller Customer</p>
                            <h6 class="mb-1 fs-14">{{ $invoice->sellerCustomer->name }}</h6>
                            <p class="mb-0 fs-13 text-muted">{{ $invoice->sellerCustomer->email }}<br>{{ $invoice->sellerCustomer->phone }}<br>{{ $invoice->sellerCustomer->address }} {{ $invoice->sellerCustomer->postcode }}</p>
                        </div>
                    </div>
                @endif
                <div class="{{ $invoice->transaction_type === 'customer_to_customer' && $invoice->sellerCustomer ? 'col-md-4' : 'col-md-6' }}">
                    <div class="info-strip h-100">
                        <p class="text-muted fs-12 text-uppercase mb-1">Buyer Customer</p>
                        <h6 class="mb-1 fs-14">{{ $invoice->customer->name }}</h6>
                        <p class="mb-0 fs-13 text-muted">{{ $invoice->customer->email }}<br>{{ $invoice->customer->phone }}<br>{{ $invoice->customer->address }} {{ $invoice->customer->postcode }}</p>
                    </div>
                </div>
                <div class="{{ $invoice->transaction_type === 'customer_to_customer' && $invoice->sellerCustomer ? 'col-md-4' : 'col-md-6' }}">
                    <div class="info-strip h-100">
                        <p class="text-muted fs-12 text-uppercase mb-1">Vehicle Details</p>
                        <h6 class="mb-1 fs-14">{{ $invoice->vehicle->make_model }}</h6>
                        <p class="mb-0 fs-13 text-muted">
                            Reg: {{ $invoice->vehicle->registration_no }}<br>
                            VIN: {{ $invoice->vehicle->vin }}<br>
                            Year: {{ $invoice->vehicle->year }} | Mileage: {{ $invoice->vehicle->mileage }} | Keys: {{ $invoice->vehicle->keys_count }}<br>
                            Type: {{ $invoice->category?->name }}
                        </p>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-4"><p class="text-muted fs-12 text-uppercase mb-1">Warranty Provider</p><h6 class="mb-0 fs-14">{{ $invoice->warranty_provider_name ?? 'No Warranty' }}</h6></div>
                <div class="col-md-4"><p class="text-muted fs-12 text-uppercase mb-1">Warranty Duration</p><h6 class="mb-0 fs-14">{{ $invoice->warranty_duration_months !== null ? $invoice->warranty_duration_months.' months' : '-' }}</h6></div>
                <div class="col-md-4"><p class="text-muted fs-12 text-uppercase mb-1">Total Paid</p><h6 class="mb-0 fs-14 fw-semibold">{{ $currency }} {{ number_format($invoice->total_paid,2) }}</h6></div>
            </div>

            <hr class="my-4">

            <div class="section-label">Totals &amp; Payments</div>
            <div class="table-responsive">
                <table class="table invoice-table align-middle mb-0">
                    <tbody>
                        <tr><td class="text-muted">Vehicle Price</td><td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->vehicle_price,2) }}</td></tr>
                        <tr><td class="text-muted">Discount</td><td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->discount,2) }}</td></tr>
                        <tr>
                            <td class="text-muted">Commission @if($invoice->commission_notes)<small class="text-muted d-block">{{ $invoice->commission_notes }}</small>@endif</td>
                            <td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->commission_amount,2) }}</td>
                        </tr>
                        <tr><td class="fw-semibold">Total Sale Price</td><td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->total_sale_price,2) }}</td></tr>
                        @foreach($invoice->payments as $p)
                            <tr>
                                <td>{{ $p->payment_date->format('d/m/Y') }} {{ str($p->payment_type)->replace('_',' ')->title() }}<small class="text-muted d-block">{{ $p->method }}</small></td>
                                <td class="text-end fw-semibold">{{ $currency }} {{ number_format($p->amount,2) }}</td>
                            </tr>
                        @endforeach
                        <tr><td class="text-muted">Total Paid</td><td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->total_paid,2) }}</td></tr>
                        <tr><td class="fw-semibold">Balance</td><td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->balance_amount,2) }}</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="invoice-bottom">
                <div>
                    @if($invoice->vehicle_terms)
                        <div class="info-box"><span>Vehicle Terms</span><p>{!! nl2br(e($invoice->vehicle_terms)) !!}</p></div>
                    @endif
                    @if($invoice->general_terms)
                        <div class="info-box"><span>General Terms</span><p>{!! nl2br(e($invoice->general_terms)) !!}</p></div>
                    @endif
                </div>
                <div class="total-card text-center">
                    @if($company?->signature_path)<img src="{{ asset('storage/'.$company->signature_path) }}" alt="Signature" style="max-height:58px;max-width:180px;object-fit:contain">@endif
                    @if($company?->stamp_path)<img src="{{ asset('storage/'.$company->stamp_path) }}" alt="Stamp" style="max-height:64px;max-width:110px;object-fit:contain;margin-left:10px">@endif
                    <div class="text-muted fs-12 mt-2">{{ $company?->authorized_person ?: $company?->manager_name ?: 'Authorized Signatory' }}</div>
                    @if($company?->designation)<div class="text-muted fs-12">{{ $company->designation }}</div>@endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
