@extends('layouts.print')

@section('title', $invoice->invoice_no)
@section('toolbar_title', 'Invoice ' . $invoice->invoice_no)
@section('toolbar_subtitle', ($invoice->customer?->name ?? 'Customer') . ' - ' . ($invoice->invoice_date?->format('d M Y') ?? ''))
@if (!empty($backUrl))
    @section('toolbar_back', $backUrl)
@endif

@section('content')
@php
    $company = $invoice->company;
    $customer = $invoice->customer;
    $seller = $invoice->sellerCustomer;
    $vehicle = $invoice->vehicle;
    $currency = $company?->currency?->symbol ?? '£';
    $money = fn ($value) => $currency . ' ' . number_format((float) $value, 2);
    $publicUrl = $publicUrl ?? route('public.vehicle-invoices.print', $invoice);
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=170x170&margin=8&data=' . rawurlencode($publicUrl);
    $settings = \App\Models\Setting::pluck('value', 'key');
    $accent = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($settings['accent_color'] ?? '')) ? $settings['accent_color'] : '#0f766e';
    $status = str((string) $invoice->payment_status)->replace('_', ' ')->title();
    $isC2C = $invoice->transaction_type === 'customer_to_customer' && $seller;
@endphp

<style>
    :root {
        --vi-accent: {{ $accent }};
        --vi-ink: #17212b;
        --vi-text: #334155;
        --vi-muted: #64748b;
        --vi-line: #d8e0e8;
        --vi-soft: #f6f8fb;
    }

    .doc.invoice-v2 {
        max-width: 880px;
        margin: 0 auto;
        background: #fff;
        color: var(--vi-text);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        line-height: 1.45;
    }

    .invoice-v2 * { box-sizing: border-box; }
    .invoice-v2 p { margin: 0; }
    .invoice-v2 strong { color: var(--vi-ink); }

    .vi-top {
        display: grid;
        grid-template-columns: 1.25fr .75fr;
        gap: 22px;
        padding: 28px 30px 20px;
        border-bottom: 5px solid var(--vi-accent);
    }

    .vi-brand {
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .vi-logo {
        width: 74px;
        height: 74px;
        display: grid;
        place-items: center;
        border: 1px solid var(--vi-line);
        background: #fff;
        overflow: hidden;
    }

    .vi-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .vi-logo-fallback {
        color: var(--vi-accent);
        font-size: 30px;
        font-weight: 900;
    }

    .vi-company-name {
        margin: 0 0 7px;
        color: var(--vi-ink);
        font-size: 24px;
        line-height: 1.05;
        font-weight: 900;
    }

    .vi-company-lines {
        display: grid;
        gap: 2px;
        color: var(--vi-muted);
        font-size: 11px;
    }

    .vi-title {
        text-align: right;
    }

    .vi-title h1 {
        margin: 0;
        color: var(--vi-ink);
        font-size: 38px;
        line-height: 1;
        font-weight: 900;
        letter-spacing: 0;
    }

    .vi-number {
        margin-top: 8px;
        color: var(--vi-accent);
        font-size: 15px;
        font-weight: 900;
    }

    .vi-status {
        display: inline-block;
        margin-top: 11px;
        padding: 5px 10px;
        background: color-mix(in srgb, var(--vi-accent) 14%, white);
        color: var(--vi-accent);
        border: 1px solid color-mix(in srgb, var(--vi-accent) 35%, white);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .vi-meta {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        margin: 0 30px;
        border: 1px solid var(--vi-line);
        border-top: 0;
    }

    .vi-meta div {
        padding: 10px 12px;
        border-right: 1px solid var(--vi-line);
    }

    .vi-meta div:last-child { border-right: 0; }
    .vi-label {
        display: block;
        margin-bottom: 3px;
        color: var(--vi-muted);
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .vi-value {
        display: block;
        color: var(--vi-ink);
        font-size: 12px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .vi-body {
        padding: 18px 30px 28px;
    }

    .vi-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .vi-card {
        border: 1px solid var(--vi-line);
        background: #fff;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .vi-card-head {
        padding: 8px 11px;
        background: var(--vi-soft);
        border-bottom: 1px solid var(--vi-line);
        color: var(--vi-accent);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .35px;
    }

    .vi-card-body {
        padding: 11px;
    }

    .vi-name {
        margin-bottom: 7px;
        color: var(--vi-ink);
        font-size: 16px;
        font-weight: 900;
    }

    .vi-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px 12px;
    }

    .vi-full { grid-column: 1 / -1; }

    .vi-section {
        margin-top: 16px;
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .vi-section-title {
        margin: 0 0 7px;
        color: var(--vi-ink);
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .35px;
    }

    .vi-money-layout {
        display: grid;
        grid-template-columns: 1fr 260px;
        gap: 14px;
    }

    .vi-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid var(--vi-line);
    }

    .vi-table th {
        padding: 8px 10px;
        background: var(--vi-ink);
        color: #fff;
        font-size: 10px;
        text-align: left;
        text-transform: uppercase;
    }

    .vi-table td {
        padding: 8px 10px;
        border-top: 1px solid var(--vi-line);
        font-size: 11px;
        vertical-align: top;
    }

    .vi-table th:last-child,
    .vi-table td:last-child { text-align: right; }

    .vi-total-box {
        border: 1px solid var(--vi-line);
    }

    .vi-total-row,
    .vi-total-main {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 9px 11px;
        border-bottom: 1px solid var(--vi-line);
    }

    .vi-total-main {
        background: var(--vi-accent);
        color: #fff;
        border-bottom: 0;
        font-size: 15px;
        font-weight: 900;
    }

    .vi-total-main strong { color: #fff; }

    .vi-public {
        display: grid;
        grid-template-columns: 132px 1fr;
        gap: 16px;
        align-items: center;
        padding: 14px;
        border: 1px solid var(--vi-line);
        background: var(--vi-soft);
    }

    .vi-qr {
        width: 118px;
        height: 118px;
        padding: 6px;
        background: #fff;
        border: 1px solid var(--vi-line);
    }

    .vi-qr img {
        width: 100%;
        height: 100%;
        display: block;
    }

    .vi-url {
        margin-top: 5px;
        color: var(--vi-accent);
        font-size: 10.5px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .vi-copy {
        color: var(--vi-muted);
        font-size: 11px;
    }

    .vi-text-box {
        padding: 11px;
        border: 1px solid var(--vi-line);
        white-space: pre-line;
    }

    .vi-signatures {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 34px;
        margin-top: 30px;
    }

    .vi-signature-images {
        height: 62px;
        display: flex;
        align-items: end;
        gap: 10px;
    }

    .vi-signature-images img {
        max-width: 128px;
        max-height: 58px;
        object-fit: contain;
    }

    .vi-line {
        border-top: 1px solid var(--vi-ink);
        padding-top: 7px;
    }

    .vi-footer {
        padding: 11px 30px;
        border-top: 1px solid var(--vi-line);
        color: var(--vi-muted);
        font-size: 10.5px;
        text-align: center;
    }

    @media print {
        .doc.invoice-v2 {
            max-width: none;
            font-size: 11px;
        }

        .vi-top { padding-top: 0; }
        .vi-body { padding-bottom: 12px; }
    }
</style>

<div class="doc invoice-v2">
    <div class="vi-top">
        <div class="vi-brand">
            <div class="vi-logo">
                @if ($company?->logo_path)
                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }}">
                @else
                    <div class="vi-logo-fallback">{{ str($company?->name ?? 'C')->substr(0, 1)->upper() }}</div>
                @endif
            </div>
            <div>
                <h2 class="vi-company-name">{{ $company?->name ?? config('app.name') }}</h2>
                <div class="vi-company-lines">
                    @if ($company?->address || $company?->city)
                        <span>{{ trim(($company?->address ?? '') . ' ' . ($company?->city ?? '')) }}</span>
                    @endif
                    @if ($company?->phone)
                        <span>{{ $company->phone }}</span>
                    @endif
                    @if ($company?->company_email)
                        <span>{{ $company->company_email }}</span>
                    @endif
                    @if ($company?->website)
                        <span>{{ $company->website }}</span>
                    @endif
                    @if ($company?->tax_number)
                        <span>VAT No: {{ $company->tax_number }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="vi-title">
            <h1>INVOICE</h1>
            <div class="vi-number">{{ $invoice->invoice_no }}</div>
            <div class="vi-status">{{ $status }}</div>
        </div>
    </div>

    <div class="vi-meta">
        <div>
            <span class="vi-label">Invoice Date</span>
            <strong class="vi-value">{{ $invoice->invoice_date?->format('d M Y') ?? '-' }}</strong>
        </div>
        <div>
            <span class="vi-label">Sale Date</span>
            <strong class="vi-value">{{ $invoice->sale_date?->format('d M Y') ?? '-' }}</strong>
        </div>
        <div>
            <span class="vi-label">Transaction</span>
            <strong class="vi-value">{{ str($invoice->transaction_type)->replace('_', ' ')->title() }}</strong>
        </div>
        <div>
            <span class="vi-label">Balance Due</span>
            <strong class="vi-value">{{ $money($invoice->balance_amount) }}</strong>
        </div>
    </div>

    <div class="vi-body">
        <div class="vi-grid">
            <div class="vi-card">
                <div class="vi-card-head">Buyer Details</div>
                <div class="vi-card-body">
                    <div class="vi-name">{{ $customer?->name ?? '-' }}</div>
                    <div class="vi-detail-grid">
                        <div>
                            <span class="vi-label">Phone</span>
                            <span class="vi-value">{{ $customer?->phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Email</span>
                            <span class="vi-value">{{ $customer?->email ?? '-' }}</span>
                        </div>
                        <div class="vi-full">
                            <span class="vi-label">Address</span>
                            <span class="vi-value">{{ trim(($customer?->address ?? '') . ' ' . ($customer?->postcode ?? '')) ?: '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="vi-card">
                <div class="vi-card-head">Vehicle Details</div>
                <div class="vi-card-body">
                    <div class="vi-name">{{ $vehicle?->make_model ?? '-' }}</div>
                    <div class="vi-detail-grid">
                        <div>
                            <span class="vi-label">Registration</span>
                            <span class="vi-value">{{ $vehicle?->registration_no ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">VIN</span>
                            <span class="vi-value">{{ $vehicle?->vin ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Year</span>
                            <span class="vi-value">{{ $vehicle?->year ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Mileage</span>
                            <span class="vi-value">{{ $vehicle?->mileage !== null ? number_format((float) $vehicle->mileage) : '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Keys</span>
                            <span class="vi-value">{{ $vehicle?->keys_count ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Category</span>
                            <span class="vi-value">{{ $invoice->category?->name ?? $vehicle?->category?->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($isC2C)
            <div class="vi-section">
                <div class="vi-card">
                    <div class="vi-card-head">Seller Details</div>
                    <div class="vi-card-body vi-detail-grid">
                        <div>
                            <span class="vi-label">Name</span>
                            <span class="vi-value">{{ $seller?->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Phone</span>
                            <span class="vi-value">{{ $seller?->phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Email</span>
                            <span class="vi-value">{{ $seller?->email ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Postcode</span>
                            <span class="vi-value">{{ $seller?->postcode ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="vi-section">
            <h3 class="vi-section-title">Charges</h3>
            <div class="vi-money-layout">
                <table class="vi-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th style="width: 150px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Vehicle Price</td>
                            <td>{{ $money($invoice->vehicle_price) }}</td>
                        </tr>
                        @if ((float) $invoice->tax !== 0.0)
                            <tr>
                                <td>Tax</td>
                                <td>{{ $money($invoice->tax) }}</td>
                            </tr>
                        @endif
                        @if ((float) $invoice->commission_amount !== 0.0)
                            <tr>
                                <td>Commission</td>
                                <td>{{ $money($invoice->commission_amount) }}</td>
                            </tr>
                        @endif
                        @if ((float) $invoice->discount !== 0.0)
                            <tr>
                                <td>Discount</td>
                                <td>-{{ $money($invoice->discount) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td><strong>Total Sale Price</strong></td>
                            <td><strong>{{ $money($invoice->total_sale_price) }}</strong></td>
                        </tr>
                    </tbody>
                </table>

                <div class="vi-total-box">
                    <div class="vi-total-row">
                        <span>Total Sale</span>
                        <strong>{{ $money($invoice->total_sale_price) }}</strong>
                    </div>
                    <div class="vi-total-row">
                        <span>Total Paid</span>
                        <strong>{{ $money($invoice->total_paid) }}</strong>
                    </div>
                    <div class="vi-total-main">
                        <span>Balance</span>
                        <strong>{{ $money($invoice->balance_amount) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @if ($invoice->payments && $invoice->payments->count())
            <div class="vi-section">
                <h3 class="vi-section-title">Payment History</h3>
                <table class="vi-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Method</th>
                            <th>Notes</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->payments as $payment)
                            <tr>
                                <td>{{ optional($payment->payment_date)->format('d M Y') ?? '-' }}</td>
                                <td>{{ str($payment->payment_type ?? 'Payment')->replace('_', ' ')->title() }}</td>
                                <td>{{ $payment->method ?? '-' }}</td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                                <td><strong>{{ $money($payment->amount) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="vi-section">
            <div class="vi-public">
                <div class="vi-qr">
                    <img src="{{ $qrUrl }}" alt="Invoice QR code">
                </div>
                <div>
                    <h3 class="vi-section-title">Public Invoice Link</h3>
                    <p class="vi-copy">Scan this QR code to open the latest public invoice print page.</p>
                    <p class="vi-url">{{ $publicUrl }}</p>
                </div>
            </div>
        </div>

        @if ($invoice->warranty_provider_name || $invoice->warranty_duration_months || $invoice->warranty_terms)
            <div class="vi-section">
                <h3 class="vi-section-title">Warranty</h3>
                <div class="vi-card">
                    <div class="vi-card-body vi-detail-grid">
                        <div>
                            <span class="vi-label">Provider</span>
                            <span class="vi-value">{{ $invoice->warranty_provider_name ?? $invoice->warrantyProvider?->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="vi-label">Duration</span>
                            <span class="vi-value">{{ $invoice->warranty_duration_months !== null ? $invoice->warranty_duration_months . ' Months' : '-' }}</span>
                        </div>
                        @if ($invoice->warranty_terms)
                            <div class="vi-full">
                                <span class="vi-label">Terms</span>
                                <span class="vi-value">{{ $invoice->warranty_terms }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($invoice->vehicle_terms)
            <div class="vi-section">
                <h3 class="vi-section-title">Vehicle Terms</h3>
                <div class="vi-text-box">{{ $invoice->vehicle_terms }}</div>
            </div>
        @endif

        @if ($invoice->general_terms)
            <div class="vi-section">
                <h3 class="vi-section-title">General Terms</h3>
                <div class="vi-text-box">{{ $invoice->general_terms }}</div>
            </div>
        @endif

        @if ($invoice->notes)
            <div class="vi-section">
                <h3 class="vi-section-title">Notes</h3>
                <div class="vi-text-box">{{ $invoice->notes }}</div>
            </div>
        @endif

        <div class="vi-signatures">
            <div>
                <div class="vi-signature-images"></div>
                <div class="vi-line">
                    <strong>{{ $customer?->name ?? 'Customer' }}</strong><br>
                    <span class="vi-copy">Customer Signature</span>
                </div>
            </div>
            <div>
                <div class="vi-signature-images">
                    @if ($company?->signature_path)
                        <img src="{{ asset('storage/' . $company->signature_path) }}" alt="Signature">
                    @endif
                    @if ($company?->stamp_path)
                        <img src="{{ asset('storage/' . $company->stamp_path) }}" alt="Company Stamp">
                    @endif
                </div>
                <div class="vi-line">
                    <strong>{{ $company?->authorized_person ?? $company?->manager_name ?? 'Authorized Person' }}</strong><br>
                    <span class="vi-copy">{{ $company?->designation ?? 'Authorized Signature' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="vi-footer">
        Please quote invoice <strong>{{ $invoice->invoice_no }}</strong> with any payment or enquiry.
        @if ($company?->phone) {{ $company->phone }} @endif
        @if ($company?->company_email) | {{ $company->company_email }} @endif
    </div>
</div>
@endsection
