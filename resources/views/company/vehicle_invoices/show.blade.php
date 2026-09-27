@extends('layouts.app')
@section('title',$invoice->invoice_no)
@section('page_title','Vehicle Sales Invoice')
@section('content')
@php $currency=$invoice->company?->currency?->symbol ?? '£'; @endphp
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">{{ $invoice->invoice_no }}</h5>
                    <p class="text-muted mb-0 fs-13">{{ $invoice->company?->name ?? 'Car Hive Ltd' }}</p>
                </div>
                <div class="d-flex align-items-center gap-1">
                    @if($invoice->payment_status === 'paid')
                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold me-1">
                            <i class="ti ti-circle-check me-1"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}
                        </span>
                    @elseif($invoice->payment_status === 'part_paid')
                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold me-1">
                            <i class="ti ti-clock me-1"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}
                        </span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold me-1">
                            <i class="ti ti-circle-x me-1"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}
                        </span>
                    @endif
                    <button type="button" class="btn btn-soft-secondary btn-sm" onclick="window.print()">
                        <i class="ti ti-printer me-1"></i>Print
                    </button>
                    <a class="btn btn-light btn-sm" href="{{ route('company.vehicle-invoices.index') }}">Back</a>
                    <a class="btn btn-primary btn-sm" href="{{ route('company.vehicle-invoices.edit',$invoice) }}">
                        <i class="ti ti-pencil me-1"></i>Edit
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-file-invoice"></i></div>
                    <div>
                        <h6>Invoice Details</h6>
                        <p>{{ $invoice->company?->company_email }} {{ $invoice->company?->phone ? '· '.$invoice->company?->phone : '' }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <p class="text-muted fs-12 text-uppercase mb-1">Invoice Date</p>
                        <h6 class="mb-0 fs-14">{{ $invoice->invoice_date->format('d M Y') }}</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted fs-12 text-uppercase mb-1">Sale Date</p>
                        <h6 class="mb-0 fs-14">{{ $invoice->sale_date?->format('d M Y') ?? '-' }}</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted fs-12 text-uppercase mb-1">Transaction</p>
                        <h6 class="mb-0 fs-14">{{ str($invoice->transaction_type)->replace('_',' ')->title() }}</h6>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-strip h-100">
                            <p class="text-muted fs-12 text-uppercase mb-1">Customer Details</p>
                            <h6 class="mb-1 fs-14">{{ $invoice->customer->name }}</h6>
                            <p class="mb-0 fs-13 text-muted">
                                {{ $invoice->customer->email }}<br>
                                {{ $invoice->customer->phone }}<br>
                                {{ $invoice->customer->address }} {{ $invoice->customer->postcode }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
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
                    <div class="col-md-4">
                        <p class="text-muted fs-12 text-uppercase mb-1">Warranty Provider</p>
                        <h6 class="mb-0 fs-14">{{ $invoice->warranty_provider_name ?? 'No Warranty' }}</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted fs-12 text-uppercase mb-1">Warranty Duration</p>
                        <h6 class="mb-0 fs-14">{{ $invoice->warranty_duration_months !== null ? $invoice->warranty_duration_months.' months' : '-' }}</h6>
                    </div>
                    <div class="col-md-4">
                        <p class="text-muted fs-12 text-uppercase mb-1">Balance</p>
                        <h6 class="mb-0 fs-14 fw-semibold">{{ $currency }} {{ number_format($invoice->balance_amount,2) }}</h6>
                    </div>
                    @if($invoice->warranty_terms)
                        <div class="col-12">
                            <p class="text-muted fs-12 text-uppercase mb-1">Warranty Terms</p>
                            <div class="fs-13">{!! nl2br(e($invoice->warranty_terms)) !!}</div>
                        </div>
                    @endif
                    @if($invoice->vehicle_terms)
                        <div class="col-12">
                            <p class="text-muted fs-12 text-uppercase mb-1">Vehicle-Specific Terms</p>
                            <div class="fs-13">{!! nl2br(e($invoice->vehicle_terms)) !!}</div>
                        </div>
                    @endif
                    @if($invoice->general_terms)
                        <div class="col-12">
                            <p class="text-muted fs-12 text-uppercase mb-1">General Terms &amp; Conditions</p>
                            <div class="fs-13">{!! nl2br(e($invoice->general_terms)) !!}</div>
                        </div>
                    @endif
                </div>

                <hr class="my-4">

                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-cash"></i></div>
                    <div>
                        <h6>Totals &amp; Payments</h6>
                        <p>Sale totals and payments received.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted">Vehicle Price</td>
                                <td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->vehicle_price,2) }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Discount</td>
                                <td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->discount,2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Total Sale Price</td>
                                <td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->total_sale_price,2) }}</td>
                            </tr>
                            @foreach($invoice->payments as $p)
                                <tr>
                                    <td>
                                        {{ $p->payment_date->format('d/m/Y') }} {{ str($p->payment_type)->replace('_',' ')->title() }}
                                        <small class="text-muted d-block">{{ $p->method }}</small>
                                    </td>
                                    <td class="text-end fw-semibold">{{ $currency }} {{ number_format($p->amount,2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="text-muted">Total Paid</td>
                                <td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->total_paid,2) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Balance</td>
                                <td class="text-end fw-semibold">{{ $currency }} {{ number_format($invoice->balance_amount,2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
