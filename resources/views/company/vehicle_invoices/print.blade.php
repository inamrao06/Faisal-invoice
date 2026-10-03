@extends('layouts.print')

@section('title', $invoice->invoice_no)

@section('toolbar_title', 'Invoice ' . $invoice->invoice_no)

@section('toolbar_subtitle', ($invoice->customer?->name ?? 'Customer') . ' · ' . ($invoice->invoice_date?->format('d M
    Y') ?? ''))

@section('toolbar_back', route('company.vehicle-invoices.show', $invoice))

@section('content')

    @php

        /* =========================================================
       BASIC DATA
    ========================================================= */

        $company = $invoice->company;

        $currency = $company?->currency?->symbol ?? '£';

        $customTemplate = \App\Models\CompanyDocumentTemplate::defaultFor($invoice->branch_id, 'invoice');

        $settings = \App\Models\Setting::pluck('value', 'key');

        $accent = preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($settings['accent_color'] ?? ''))
            ? $settings['accent_color']
            : '#1D4ED8';

        $money = fn($value) => $currency . ' ' . number_format((float) $value, 2);

        $publicUrl = $publicUrl ?? route('company.vehicle-invoices.print', $invoice);

        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=170x170&margin=8&data=' . rawurlencode($publicUrl);

        /* =========================================================
       STATUS
    ========================================================= */

        $status = $invoice->payment_status;

        $statusClass =
            $status === 'paid' ? 'is-paid' : (in_array($status, ['part_paid', 'deposit'], true) ? 'is-part' : 'is-due');

        $statusIcon =
            $status === 'paid'
                ? 'ti-circle-check'
                : (in_array($status, ['part_paid', 'deposit'], true)
                    ? 'ti-clock'
                    : 'ti-alert-circle');

        /* =========================================================
       CONTACT
    ========================================================= */

        $addressLine = (string) ($company?->address ?? '');

        if (filled($company?->city) && !str_contains(mb_strtolower($addressLine), mb_strtolower($company->city))) {
            $addressLine = trim($addressLine . ', ' . $company->city);
        }

        $contactLines = collect([
            ['ti-map-pin', $addressLine],

            ['ti-phone', $company?->phone],

            ['ti-mail', $company?->company_email],

            ['ti-world', $company?->website],

            ['ti-file-certificate', $company?->tax_number ? 'VAT No: ' . $company->tax_number : null],
        ])->filter(fn($line) => filled($line[1]));

        /* =========================================================
       MODELS
    ========================================================= */

        $customer = $invoice->customer;

        $seller = $invoice->sellerCustomer;

        $vehicle = $invoice->vehicle;

        $isC2C = $invoice->transaction_type === 'customer_to_customer' && $seller;

        /* =========================================================
       WARRANTY
    ========================================================= */

        $warrantyProvider = $invoice->warranty_provider_id
            ? DB::table('warranty_providers')->find($invoice->warranty_provider_id)
            : null;

        $warrantyName = $invoice->warranty_provider_name ?: $warrantyProvider?->name ?? null;

        $warrantyDuration =
            $invoice->warranty_duration_months !== null ? $invoice->warranty_duration_months . ' Months' : null;

        $warrantyDescription = $invoice->warranty_provider_description ?: $warrantyProvider?->description ?? null;

        $hasWarranty = filled($warrantyName) || filled($warrantyDuration) || filled($warrantyDescription);

        /* =========================================================
       AMOUNTS
    ========================================================= */

        $vehiclePrice = (float) $invoice->vehicle_price;

        $tax = (float) $invoice->tax;

        $discount = (float) $invoice->discount;

        $commission = (float) $invoice->commission_amount;

        $totalSalePrice = (float) $invoice->total_sale_price;

        $totalPaid = (float) $invoice->total_paid;

        $balance = (float) $invoice->balance_amount;
    @endphp


    @if ($customTemplate)

        {!! $customTemplate->render(\App\Models\CompanyDocumentTemplate::invoiceValues($invoice)) !!}
    @else
        <style>
            /* =========================================================
           INVOICE BASE
        ========================================================= */

            :root {
                --invoice-accent: {{ $accent }};
                --invoice-dark: #18222d;
                --invoice-text: #334155;
                --invoice-muted: #687585;
                --invoice-border: #dce2e8;
                --invoice-soft: #f6f8fa;
                --invoice-green: #15803d;
                --invoice-red: #b91c1c;
                --invoice-orange: #b45309;
            }

            * {
                box-sizing: border-box;
            }

            .vehicle-invoice {
                width: 100%;
                max-width: 1100px;
                margin: 0 auto;
                background: #fff;
                color: var(--invoice-text);
                font-family: Arial, Helvetica, sans-serif;
                font-size: 11px;
                line-height: 1.35;
            }

            .vehicle-invoice strong {
                font-weight: 800;
            }


            /* =========================================================
           HEADER
        ========================================================= */

            .vi-header {
                display: flex;
                justify-content: space-between;
                align-items: stretch;
                gap: 0;

                padding: 0;

                border-bottom: 4px solid var(--invoice-accent);

                background: var(--invoice-dark);
            }

            .vi-company {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                min-width: 0;
                flex: 1;
                padding: 12px 14px 10px;
            }

            .vi-logo {
                width: 54px;
                height: 54px;

                flex: 0 0 54px;

                display: flex;
                align-items: center;
                justify-content: center;

                overflow: hidden;

                border: 2px solid rgba(255, 255, 255, .45);
                border-radius: 4px;

                background: #fff;
            }

            .vi-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            .vi-logo-fallback {
                width: 100%;
                height: 100%;

                display: flex;
                align-items: center;
                justify-content: center;

                background: #fff;

                color: var(--invoice-accent);

                font-size: 25px;
                font-weight: 900;
            }

            .vi-company-name {
                margin: 0 0 4px;

                color: #fff;

                font-size: 18px;
                line-height: 1.1;

                font-weight: 900;
            }

            .vi-contact {
                margin: 0;
                padding: 0;

                list-style: none;
            }

            .vi-contact li {
                display: flex;
                align-items: flex-start;
                gap: 5px;

                margin: 1px 0;

                color: #d7dee7;

                font-size: 8.5px;

                word-break: break-word;
            }

            .vi-contact i {
                width: 12px;
                flex: 0 0 12px;

                color: #fff;
            }

            .vi-heading {
                min-width: 220px;
                padding: 12px 14px 10px;
                text-align: right;
                background: var(--invoice-soft);
                border-left: 4px solid var(--invoice-accent);
            }

            .vi-kicker {
                color: var(--invoice-accent);

                font-size: 9px;
                font-weight: 800;

                text-transform: uppercase;
                letter-spacing: .8px;
            }

            .vi-heading h1 {
                margin: 2px 0 4px;

                color: var(--invoice-dark);

                font-size: 28px;
                line-height: 1;

                font-weight: 900;
            }

            .vi-invoice-no {
                color: var(--invoice-dark);
                font-size: 12px;
                font-weight: 800;
            }

            .vi-invoice-no span {
                color: var(--invoice-muted);
                margin-right: 4px;
                font-weight: 500;
            }

            .vi-status {
                display: inline-flex;
                align-items: center;
                gap: 4px;

                margin-top: 6px;
                padding: 3px 9px;

                border-radius: 20px;

                font-size: 8px;
                font-weight: 900;

                text-transform: uppercase;
            }

            .vi-status.is-paid {
                color: #166534;
                background: #dcfce7;
            }

            .vi-status.is-part {
                color: #92400e;
                background: #fef3c7;
            }

            .vi-status.is-due {
                color: #991b1b;
                background: #fee2e2;
            }


            /* =========================================================
           META
        ========================================================= */

            .vi-meta {
                display: grid;
                grid-template-columns: repeat(4, 1fr);

                margin: 0 16px;

                border: 1px solid var(--invoice-border);
                border-top: 0;

                background: var(--invoice-border);
            }

            .vi-meta-item {
                padding: 7px 9px;

                background: #fff;
            }

            .vi-meta-item span {
                display: block;

                margin-bottom: 1px;

                color: var(--invoice-muted);

                font-size: 7.5px;
                font-weight: 800;

                text-transform: uppercase;
                letter-spacing: .3px;
            }

            .vi-meta-item strong {
                color: var(--invoice-dark);

                font-size: 10px;
                font-weight: 900;
            }

            .vi-meta-item.accent {
                background: var(--invoice-accent);
            }

            .vi-meta-item.accent span,
            .vi-meta-item.accent strong {
                color: #fff;
            }


            /* =========================================================
           BODY
        ========================================================= */

            .vi-body {
                padding: 8px 14px 10px;
            }

            .vi-section-title {
                display: flex;
                align-items: center;
                gap: 5px;

                margin: 0 0 5px;

                color: var(--invoice-dark);

                font-size: 9px;
                font-weight: 900;

                text-transform: uppercase;
                letter-spacing: .35px;
            }

            .vi-section-title i {
                color: var(--invoice-accent);
                font-size: 13px;
            }


            /* =========================================================
           CUSTOMER / VEHICLE
        ========================================================= */

            .vi-info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;

                gap: 8px;

                margin-bottom: 8px;
            }

            .vi-card {
                border: 1px solid var(--invoice-border);
                border-radius: 6px;

                overflow: hidden;

                background: #fff;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-card-title {
                padding: 5px 8px;

                border-bottom: 1px solid var(--invoice-border);

                background: var(--invoice-soft);

                color: var(--invoice-accent);

                font-size: 8px;
                font-weight: 900;

                text-transform: uppercase;
                letter-spacing: .4px;
            }

            .vi-card-body {
                padding: 7px 8px;
            }

            .vi-primary-name {
                margin-bottom: 4px;

                color: var(--invoice-dark);

                font-size: 12px;
                font-weight: 900;
            }

            .vi-details {
                display: grid;
                grid-template-columns: 1fr 1fr;

                gap: 3px 10px;
            }

            .vi-detail {
                min-width: 0;

                overflow-wrap: anywhere;
            }

            .vi-detail.full {
                grid-column: 1 / -1;
            }

            .vi-detail-label {
                color: var(--invoice-muted);
                font-size: 8px;
                font-weight: 600;
            }

            .vi-detail-value {
                color: var(--invoice-dark);
                font-size: 9px;
                font-weight: 800;
            }


            /* =========================================================
           SELLER
        ========================================================= */

            .vi-seller {
                margin-bottom: 8px;

                border: 1px solid var(--invoice-border);
                border-radius: 6px;

                overflow: hidden;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-seller-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
            }

            .vi-seller-item {
                padding: 6px 8px;

                border-right: 1px solid var(--invoice-border);
            }

            .vi-seller-item:last-child {
                border-right: 0;
            }


            /* =========================================================
           CHARGES
        ========================================================= */

            .vi-charges {
                margin-top: 8px;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-charges-layout {
                display: grid;
                grid-template-columns: minmax(0, 1fr) 220px;

                gap: 9px;
            }

            .vi-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            .vi-table th {
                padding: 5px 7px;

                background: var(--invoice-dark);

                color: #fff;

                font-size: 8px;
                font-weight: 900;

                text-align: left;
                text-transform: uppercase;
            }

            .vi-table th:last-child,
            .vi-table td:last-child {
                text-align: right;
            }

            .vi-table td {
                padding: 5px 7px;

                border-bottom: 1px solid var(--invoice-border);

                color: var(--invoice-text);

                font-size: 9px;
                font-weight: 600;

                vertical-align: top;
            }

            .vi-table td:first-child {
                color: var(--invoice-dark);
                font-weight: 800;
            }

            .vi-total-box {
                border: 1px solid var(--invoice-border);
                border-radius: 6px;

                overflow: hidden;
            }

            .vi-total-row {
                display: flex;
                justify-content: space-between;
                gap: 8px;

                padding: 6px 8px;

                border-bottom: 1px solid var(--invoice-border);
            }

            .vi-total-row span {
                color: var(--invoice-muted);
                font-size: 9px;
                font-weight: 600;
            }

            .vi-total-row strong {
                color: var(--invoice-dark);
                font-size: 9px;
                font-weight: 900;
                text-align: right;
            }

            .vi-total-main {
                display: flex;
                justify-content: space-between;
                gap: 8px;

                padding: 8px;

                background: var(--invoice-accent);

                color: #fff;
            }

            .vi-total-main span,
            .vi-total-main strong {
                color: #fff;
                font-size: 10px;
                font-weight: 900;
            }

            .vi-balance {
                display: flex;
                justify-content: space-between;

                padding: 6px 8px;

                background: #fff7ed;

                color: var(--invoice-orange);

                font-size: 9px;
                font-weight: 900;
            }


            /* =========================================================
           PAYMENTS
        ========================================================= */

            .vi-payments {
                margin-top: 8px;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-payment-table {
                width: 100%;
                border-collapse: collapse;
            }

            .vi-payment-table th {
                padding: 4px 6px;

                background: var(--invoice-soft);

                border-bottom: 1px solid var(--invoice-border);

                color: var(--invoice-muted);

                font-size: 7.5px;
                font-weight: 900;

                text-align: left;
                text-transform: uppercase;
            }

            .vi-payment-table td {
                padding: 4px 6px;

                border-bottom: 1px solid var(--invoice-border);

                font-size: 8.5px;
            }

            .vi-payment-table th:last-child,
            .vi-payment-table td:last-child {
                text-align: right;
            }


            /* =========================================================
           BANK DETAILS
        ========================================================= */

            .vi-bank {
                margin-top: 7px;

                border: 1px solid var(--invoice-border);
                border-radius: 6px;

                overflow: hidden;
                display: grid;
                grid-template-columns: minmax(0, 1fr) 104px;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-bank-header {
                display: flex;
                align-items: center;
                gap: 7px;

                padding: 6px 8px;

                background: var(--invoice-dark);

                border-bottom: 1px solid rgba(255, 255, 255, .14);
                grid-column: 1 / -1;
            }

            .vi-bank-icon {
                width: 20px;
                height: 20px;

                display: flex;
                align-items: center;
                justify-content: center;

                flex: 0 0 20px;

                border-radius: 4px;

                background: #fff;

                color: var(--invoice-accent);
            }

            .vi-bank-icon i {
                font-size: 12px;
            }

            .vi-bank-title {
                color: #fff;

                font-size: 8.5px;
                font-weight: 900;

                letter-spacing: .35px;
            }

            .vi-bank-subtitle {
                margin-top: 1px;

                color: #d7dee7;

                font-size: 6.5px;
            }

            .vi-bank-grid {
                display: grid;
                grid-template-columns: 1.1fr 1.25fr .8fr 1fr;

                gap: 0;
            }

            .vi-bank-item {
                min-width: 0;

                padding: 5px 6px;

                border-right: 1px solid var(--invoice-border);
            }

            .vi-bank-item:last-child {
                border-right: 0;
            }

            .vi-bank-item.iban {
                grid-column: span 2;

                border-top: 1px solid var(--invoice-border);
            }

            .vi-bank-label {
                display: block;

                margin-bottom: 1px;

                color: var(--invoice-muted);

                font-size: 6.5px;
                font-weight: 900;

                text-transform: uppercase;
                letter-spacing: .2px;
            }

            .vi-bank-value {
                color: var(--invoice-dark);

                font-size: 8px;
                font-weight: 900;

                overflow-wrap: anywhere;
            }

            .vi-public-link {
                display: flex;
                align-items: center;
                justify-content: center;

                margin: 0;
                padding: 7px;

                border-left: 1px solid var(--invoice-border);

                background: var(--invoice-soft);

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-qr {
                width: 86px;
                height: 86px;
                padding: 4px;

                border: 1px solid var(--invoice-border);
                border-radius: 4px;

                background: #fff;
            }

            .vi-qr img {
                display: block;
                width: 100%;
                height: 100%;
            }


            /* =========================================================
           WARRANTY
        ========================================================= */

            .vi-warranty {
                margin-top: 9px;

                border: 1px solid var(--invoice-border);
                border-left: 3px solid var(--invoice-accent);

                border-radius: 6px;

                overflow: hidden;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-warranty-title {
                padding: 6px 8px;

                background: var(--invoice-soft);

                color: var(--invoice-dark);

                font-size: 9px;
                font-weight: 900;

                text-transform: uppercase;
            }

            .vi-warranty-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }

            .vi-warranty-item {
                padding: 6px 8px;

                border-top: 1px solid var(--invoice-border);
                border-right: 1px solid var(--invoice-border);
            }

            .vi-warranty-item:nth-child(2n) {
                border-right: 0;
            }

            .vi-warranty-item.full {
                grid-column: 1 / -1;
                border-right: 0;
            }

            .vi-warranty-label {
                display: block;

                margin-bottom: 2px;

                color: var(--invoice-muted);

                font-size: 7px;
                font-weight: 900;

                text-transform: uppercase;
            }

            .vi-warranty-value {
                color: var(--invoice-dark);

                font-size: 9px;
                font-weight: 900;
            }

            .vi-warranty-description {
                color: var(--invoice-dark);

                font-size: 9px;
                line-height: 1.4;

                font-weight: 800;

                white-space: pre-line;
            }


            /* =========================================================
           TERMS
        ========================================================= */

            .vi-terms {
                margin-top: 9px;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-terms-card {
                padding: 7px 8px;

                border: 1px solid var(--invoice-border);
                border-radius: 6px;

                background: #fff;
            }

            .vi-terms-title {
                margin-bottom: 4px;

                color: var(--invoice-dark);

                font-size: 8px;
                font-weight: 900;

                text-transform: uppercase;
            }

            .vi-terms-text {
                color: var(--invoice-text);

                font-size: 8px;
                line-height: 1.42;

                white-space: pre-line;
            }


            /* =========================================================
           NOTES
        ========================================================= */

            .vi-notes {
                margin-top: 8px;

                padding: 7px 8px;

                border: 1px solid var(--invoice-border);
                border-radius: 6px;

                background: var(--invoice-soft);

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-notes-title {
                margin-bottom: 2px;

                color: var(--invoice-dark);

                font-size: 8px;
                font-weight: 900;

                text-transform: uppercase;
            }

            .vi-notes-text {
                color: var(--invoice-text);

                font-size: 8px;
                font-weight: 700;

                white-space: pre-line;
            }


            /* =========================================================
           SIGNATURE
        ========================================================= */

            .vi-signatures {
                display: grid;
                grid-template-columns: 1fr 1fr;

                gap: 35px;

                margin-top: 14px;

                break-inside: avoid;
                page-break-inside: avoid;
            }

            .vi-signature {
                min-height: 68px;

                text-align: center;
            }

            .vi-signature-images {
                height: 38px;

                display: flex;
                align-items: center;
                justify-content: center;

                gap: 10px;
            }

            .vi-signature-images img {
                max-width: 85px;
                max-height: 35px;

                object-fit: contain;
            }

            .vi-signature-line {
                height: 1px;

                margin: 0 20px 3px;

                background: var(--invoice-dark);
            }

            .vi-signature-name {
                color: var(--invoice-dark);

                font-size: 8px;
                font-weight: 900;
            }

            .vi-signature-role {
                color: var(--invoice-muted);

                font-size: 7px;
            }


            /* =========================================================
           FOOTER
        ========================================================= */

            .vi-footer {
                margin: 0 16px;

                padding: 7px 0 9px;

                border-top: 1px solid var(--invoice-border);

                color: var(--invoice-muted);

                font-size: 7px;

                text-align: center;
            }


            /* =========================================================
           PRINT
        ========================================================= */

            @media print {

                @page {
                    size: A4;
                    margin: 6mm;
                }

                html,
                body {
                    margin: 0 !important;
                    padding: 0 !important;

                    background: #fff !important;
                }

                body {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }

                .vehicle-invoice {
                    width: 100%;
                    max-width: none;

                    font-size: 9px;
                    line-height: 1.18;
                }

                .vi-company,
                .vi-heading {
                    padding-top: 7px;
                    padding-bottom: 7px;
                }

                .vi-logo {
                    width: 42px;
                    height: 42px;
                    flex-basis: 42px;
                }

                .vi-company-name {
                    font-size: 14px;
                }

                .vi-heading h1 {
                    font-size: 22px;
                }

                .vi-meta {
                    margin: 0 10px;
                }

                .vi-body {
                    padding: 5px 10px 7px;
                }

                .vi-info-grid,
                .vi-charges,
                .vi-payments,
                .vi-bank,
                .vi-warranty,
                .vi-terms {
                    margin-top: 5px;
                    margin-bottom: 0;
                }

                .vi-card-title,
                .vi-card-body,
                .vi-table th,
                .vi-table td,
                .vi-payment-table th,
                .vi-payment-table td,
                .vi-total-row,
                .vi-total-main,
                .vi-balance,
                .vi-bank-header,
                .vi-bank-item,
                .vi-warranty-title,
                .vi-warranty-item,
                .vi-terms-card,
                .vi-notes {
                    padding-top: 3px;
                    padding-bottom: 3px;
                }

                .vi-qr {
                    width: 72px;
                    height: 72px;
                    padding: 3px;
                }

                .vi-bank {
                    grid-template-columns: minmax(0, 1fr) 86px;
                }

                .vi-signatures {
                    margin-top: 14px;
                }

                .vi-signature-images {
                    height: 34px;
                }

                .vi-signature-images img {
                    max-height: 32px;
                }

                .vi-footer {
                    margin: 0 10px;
                    padding-top: 4px;
                    padding-bottom: 4px;
                }

                .vi-card,
                .vi-seller,
                .vi-charges,
                .vi-payments,
                .vi-bank,
                .vi-warranty,
                .vi-terms,
                .vi-notes,
                .vi-signatures {
                    break-inside: avoid !important;
                    page-break-inside: avoid !important;
                }

                .vi-table tr,
                .vi-payment-table tr {
                    break-inside: avoid;
                    page-break-inside: avoid;
                }
            }


            /* =========================================================
           MOBILE
        ========================================================= */

            @media (max-width: 700px) {

                .vi-header {
                    flex-direction: column;
                }

                .vi-heading {
                    min-width: 0;
                    text-align: left;
                }

                .vi-meta,
                .vi-info-grid,
                .vi-charges-layout,
                .vi-signatures {
                    grid-template-columns: 1fr;
                }

                .vi-seller-grid {
                    grid-template-columns: 1fr;
                }

                .vi-details {
                    grid-template-columns: 1fr;
                }

                .vi-bank-grid {
                    grid-template-columns: 1fr 1fr;
                }

                .vi-bank {
                    grid-template-columns: 1fr;
                }

                .vi-public-link {
                    border-left: 0;
                    border-top: 1px solid var(--invoice-border);
                    justify-content: flex-start;
                }

                .vi-bank-item.iban {
                    grid-column: span 2;
                }

                .vi-warranty-grid {
                    grid-template-columns: 1fr;
                }

                .vi-warranty-item,
                .vi-warranty-item:nth-child(2n) {
                    border-right: 0;
                }
            }
        </style>


        <div class="vehicle-invoice">


            {{-- =====================================================
         HEADER
    ====================================================== --}}

            <div class="vi-header">

                <div class="vi-company">

                    <div class="vi-logo">

                        @if ($company?->logo_path)
                            <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }}">
                        @else
                            <div class="vi-logo-fallback">
                                {{ strtoupper(substr($company?->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif

                    </div>


                    <div>

                        <h2 class="vi-company-name">
                            {{ $company?->name ?? 'Car Hive Ltd.' }}
                        </h2>

                        <ul class="vi-contact">

                            @foreach ($contactLines as [$icon, $text])
                                <li>
                                    <i class="ti {{ $icon }}"></i>
                                    <span>{{ $text }}</span>
                                </li>
                            @endforeach

                        </ul>

                    </div>

                </div>


                <div class="vi-heading">

                    <div class="vi-kicker">
                        {{ str($invoice->transaction_type)->replace('_', ' ')->title() }}
                    </div>

                    <h1>INVOICE</h1>

                    <div class="vi-invoice-no">
                        <span>No:</span>
                        {{ $invoice->invoice_no }}
                    </div>

                    <span class="vi-status {{ $statusClass }}">

                        <i class="ti {{ $statusIcon }}"></i>

                        {{ str($status)->replace('_', ' ')->title() }}

                    </span>

                </div>

            </div>


            {{-- =====================================================
         META
    ====================================================== --}}

            <div class="vi-meta">

                <div class="vi-meta-item">

                    <span>Invoice Date</span>

                    <strong>
                        {{ $invoice->invoice_date?->format('d M Y') ?? '—' }}
                    </strong>

                </div>


                <div class="vi-meta-item">

                    <span>Sale Date</span>

                    <strong>
                        {{ $invoice->sale_date?->format('d M Y') ?? '—' }}
                    </strong>

                </div>


                <div class="vi-meta-item">

                    <span>Total Paid</span>

                    <strong>
                        {{ $money($totalPaid) }}
                    </strong>

                </div>


                <div class="vi-meta-item accent">

                    <span>Balance Due</span>

                    <strong>
                        {{ $money($balance) }}
                    </strong>

                </div>

            </div>


            <div class="vi-body">


                {{-- =================================================
             CUSTOMER + VEHICLE
        ================================================== --}}

                <div class="vi-info-grid">


                    {{-- CUSTOMER --}}

                    <div class="vi-card">

                        <div class="vi-card-title">
                            Customer Information
                        </div>

                        <div class="vi-card-body">

                            <div class="vi-primary-name">
                                {{ $customer?->name ?? '—' }}
                            </div>

                            <div class="vi-details">

                                @if ($customer?->phone)
                                    <div class="vi-detail">
                                        <span class="vi-detail-label">
                                            Phone:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $customer->phone }}
                                        </strong>
                                    </div>
                                @endif


                                @if ($customer?->email)
                                    <div class="vi-detail">
                                        <span class="vi-detail-label">
                                            Email:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $customer->email }}
                                        </strong>
                                    </div>
                                @endif


                                @if ($customer?->address)
                                    <div class="vi-detail full">

                                        <span class="vi-detail-label">
                                            Address:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $customer->address }}
                                        </strong>

                                    </div>
                                @endif


                                @if ($customer?->postcode)
                                    <div class="vi-detail">

                                        <span class="vi-detail-label">
                                            Postcode:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $customer->postcode }}
                                        </strong>

                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>


                    {{-- VEHICLE --}}

                    <div class="vi-card">

                        <div class="vi-card-title">
                            Vehicle Information
                        </div>

                        <div class="vi-card-body">

                            <div class="vi-primary-name">
                                {{ $vehicle?->make_model ?? 'Vehicle' }}
                            </div>

                            <div class="vi-details">


                                @if ($vehicle?->registration_no)
                                    <div class="vi-detail">

                                        <span class="vi-detail-label">
                                            Registration:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $vehicle->registration_no }}
                                        </strong>

                                    </div>
                                @endif


                                @if ($vehicle?->vin)
                                    <div class="vi-detail">

                                        <span class="vi-detail-label">
                                            VIN / Chassis:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $vehicle->vin }}
                                        </strong>

                                    </div>
                                @endif


                                @if ($vehicle?->year)
                                    <div class="vi-detail">

                                        <span class="vi-detail-label">
                                            Year:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $vehicle->year }}
                                        </strong>

                                    </div>
                                @endif


                                @if ($vehicle?->mileage !== null)
                                    <div class="vi-detail">

                                        <span class="vi-detail-label">
                                            Mileage:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ number_format((float) $vehicle->mileage) }}
                                        </strong>

                                    </div>
                                @endif


                                @if ($vehicle?->keys_count !== null)
                                    <div class="vi-detail">

                                        <span class="vi-detail-label">
                                            Keys:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $vehicle->keys_count }}
                                        </strong>

                                    </div>
                                @endif


                                @if ($vehicle?->category?->name)
                                    <div class="vi-detail full">

                                        <span class="vi-detail-label">
                                            Category:
                                        </span>

                                        <strong class="vi-detail-value">
                                            {{ $vehicle->category->name }}
                                        </strong>

                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
             SELLER
        ================================================== --}}

                @if ($isC2C)
                    <div class="vi-seller">

                        <div class="vi-card-title">
                            Seller Information
                        </div>

                        <div class="vi-seller-grid">

                            <div class="vi-seller-item">

                                <span class="vi-detail-label">
                                    Name
                                </span>

                                <strong class="vi-detail-value">
                                    {{ $seller?->name ?? '—' }}
                                </strong>

                            </div>


                            <div class="vi-seller-item">

                                <span class="vi-detail-label">
                                    Phone
                                </span>

                                <strong class="vi-detail-value">
                                    {{ $seller?->phone ?? '—' }}
                                </strong>

                            </div>


                            <div class="vi-seller-item">

                                <span class="vi-detail-label">
                                    Email
                                </span>

                                <strong class="vi-detail-value">
                                    {{ $seller?->email ?? '—' }}
                                </strong>

                            </div>

                        </div>

                    </div>
                @endif


                {{-- =================================================
             CHARGES
        ================================================== --}}

                <div class="vi-charges">

                    <div class="vi-section-title">

                        <i class="ti ti-receipt"></i>

                        Charges & Payments

                    </div>


                    <div class="vi-charges-layout">


                        <table class="vi-table">

                            <thead>

                                <tr>

                                    <th>
                                        Description
                                    </th>

                                    <th style="width: 135px;">
                                        Amount
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                <tr>

                                    <td>
                                        Vehicle Price
                                    </td>

                                    <td>
                                        {{ $money($vehiclePrice) }}
                                    </td>

                                </tr>


                                @if ($tax != 0)
                                    <tr>

                                        <td>
                                            Tax
                                        </td>

                                        <td>
                                            {{ $money($tax) }}
                                        </td>

                                    </tr>
                                @endif


                                @if ($commission != 0)
                                    <tr>

                                        <td>
                                            Commission
                                        </td>

                                        <td>
                                            {{ $money($commission) }}
                                        </td>

                                    </tr>
                                @endif


                                @if ($discount != 0)
                                    <tr>

                                        <td>
                                            Discount
                                        </td>

                                        <td>
                                            {{ $money($discount) }}
                                        </td>

                                    </tr>
                                @endif


                                <tr>

                                    <td>
                                        Total Sale Price
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $money($totalSalePrice) }}
                                        </strong>
                                    </td>

                                </tr>

                            </tbody>

                        </table>


                        <div class="vi-total-box">

                            <div class="vi-total-row">

                                <span>
                                    Total
                                </span>

                                <strong>
                                    {{ $money($totalSalePrice) }}
                                </strong>

                            </div>


                            <div class="vi-total-row">

                                <span>
                                    Total Paid
                                </span>

                                <strong>
                                    {{ $money($totalPaid) }}
                                </strong>

                            </div>


                            <div class="vi-total-main">

                                <span>
                                    TOTAL
                                </span>

                                <strong>
                                    {{ $money($totalSalePrice) }}
                                </strong>

                            </div>


                            <div class="vi-balance">

                                <span>
                                    Balance Due
                                </span>

                                <strong>
                                    {{ $money($balance) }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
             PAYMENT HISTORY
        ================================================== --}}

                @if ($invoice->payments && $invoice->payments->count())

                    <div class="vi-payments">

                        <div class="vi-section-title">

                            <i class="ti ti-credit-card"></i>

                            Payment History

                        </div>


                        <table class="vi-payment-table">

                            <thead>

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Payment Type
                                    </th>

                                    <th>
                                        Method
                                    </th>

                                    <th>
                                        Notes
                                    </th>

                                    <th>
                                        Amount
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($invoice->payments as $payment)
                                    <tr>

                                        <td>
                                            {{ optional($payment->payment_date)->format('d M Y') ?? '—' }}
                                        </td>

                                        <td>
                                            {{ str($payment->payment_type ?? 'Payment')->replace('_', ' ')->title() }}
                                        </td>

                                        <td>
                                            {{ $payment->method ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $payment->notes ?? '—' }}
                                        </td>

                                        <td>
                                            <strong>
                                                {{ $money($payment->amount) }}
                                            </strong>
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                @endif


                {{-- =================================================
             BANK ACCOUNT DETAILS
        ================================================== --}}

                <div class="vi-bank">

                    <div class="vi-bank-header">

                        <div class="vi-bank-icon">

                            <i class="ti ti-building-bank"></i>

                        </div>


                        <div>

                            <div class="vi-bank-title">
                                Bank Account Details
                            </div>

                            <div class="vi-bank-subtitle">
                                Please quote invoice number with any payment.
                            </div>

                        </div>

                    </div>


                    <div class="vi-bank-grid">


                        <div class="vi-bank-item">

                            <span class="vi-bank-label">
                                Bank Name
                            </span>

                            <strong class="vi-bank-value">
                                Lloyds Bank
                            </strong>

                        </div>


                        <div class="vi-bank-item">

                            <span class="vi-bank-label">
                                Account Name
                            </span>

                            <strong class="vi-bank-value">
                                CAR HIVE LTD
                            </strong>

                        </div>


                        <div class="vi-bank-item">

                            <span class="vi-bank-label">
                                Sort Code
                            </span>

                            <strong class="vi-bank-value">
                                30-54-66
                            </strong>

                        </div>


                        <div class="vi-bank-item">

                            <span class="vi-bank-label">
                                Account Number
                            </span>

                            <strong class="vi-bank-value">
                                18406060
                            </strong>

                        </div>


                        <div class="vi-bank-item iban">

                            <span class="vi-bank-label">
                                IBAN
                            </span>

                            <strong class="vi-bank-value">
                                GB95LOYD30546618406060
                            </strong>

                        </div>


                        <div class="vi-bank-item">

                            <span class="vi-bank-label">
                                BIC
                            </span>

                            <strong class="vi-bank-value">
                                LOYDGB21F95
                            </strong>

                        </div>

                    </div>

                <div class="vi-public-link">

                    <div class="vi-qr">
                        <img src="{{ $qrUrl }}" alt="Invoice QR code">
                    </div>

                </div>

                </div>


                {{-- =================================================
             WARRANTY
        ================================================== --}}

                @if ($hasWarranty)

                    <div class="vi-warranty">

                        <div class="vi-warranty-title">
                            Warranty Information
                        </div>


                        <div class="vi-warranty-grid">


                            @if ($warrantyName)
                                <div class="vi-warranty-item">

                                    <span class="vi-warranty-label">
                                        Warranty Provider
                                    </span>

                                    <strong class="vi-warranty-value">
                                        {{ $warrantyName }}
                                    </strong>

                                </div>
                            @endif


                            <div class="vi-warranty-item">

                                <span class="vi-warranty-label">
                                    Duration
                                </span>

                                <strong class="vi-warranty-value">
                                    {{ $warrantyDuration ?? '—' }}
                                </strong>

                            </div>


                            @if ($warrantyDescription)
                                <div class="vi-warranty-item full">

                                    <span class="vi-warranty-label">
                                        Warranty Description
                                    </span>

                                    <div class="vi-warranty-description">
                                        {{ $warrantyDescription }}
                                    </div>

                                </div>
                            @endif

                        </div>

                    </div>

                @endif


                {{-- =================================================
             TERMS
        ================================================== --}}

                @if (filled($invoice->vehicle_terms))
                    <div class="vi-terms">

                        <div class="vi-section-title">

                            <i class="ti ti-file-description"></i>

                            Terms & Conditions

                        </div>


                        <div class="vi-terms-card">

                            <div class="vi-terms-title">
                                Vehicle Terms
                            </div>

                            <div class="vi-terms-text">
                                {{ $invoice->vehicle_terms }}
                            </div>

                        </div>

                    </div>
                @endif


                {{-- =================================================
             GENERAL TERMS
        ================================================== --}}

                @if (filled($invoice->general_terms))
                    <div class="vi-terms">

                        <div class="vi-terms-card">

                            <div class="vi-terms-title">
                                General Terms & Conditions
                            </div>

                            <div class="vi-terms-text">
                                {{ $invoice->general_terms }}
                            </div>

                        </div>

                    </div>
                @endif


                {{-- =================================================
             NOTES
        ================================================== --}}

                @if (filled($invoice->notes))
                    <div class="vi-notes">

                        <div class="vi-notes-title">
                            Notes
                        </div>

                        <div class="vi-notes-text">
                            {{ $invoice->notes }}
                        </div>

                    </div>
                @endif


                {{-- =================================================
             SIGNATURES
        ================================================== --}}

                <div class="vi-signatures">


                    {{-- CUSTOMER --}}

                    <div class="vi-signature">

                        <div class="vi-signature-images"></div>

                        <div class="vi-signature-line"></div>

                        <div class="vi-signature-name">
                            {{ $customer?->name ?? 'Customer' }}
                        </div>

                        <div class="vi-signature-role">
                            Customer Signature
                        </div>

                    </div>


                    {{-- COMPANY --}}

                    <div class="vi-signature">

                        <div class="vi-signature-images">

                            @if ($company?->signature_path)
                                <img src="{{ asset('storage/' . $company->signature_path) }}"
                                    alt="Signature">
                            @endif


                            @if ($company?->stamp_path)
                                <img src="{{ asset('storage/' . $company->stamp_path) }}"
                                    alt="Company Stamp">
                            @endif

                        </div>


                        <div class="vi-signature-line"></div>


                        <div class="vi-signature-name">

                            {{ $company?->authorized_person ?? ($company?->manager_name ?? 'Authorized Person') }}

                        </div>


                        <div class="vi-signature-role">

                            {{ $company?->designation ?? 'Authorized Signature' }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
         FOOTER
    ====================================================== --}}

            <div class="vi-footer">

                Please quote invoice
                <strong>{{ $invoice->invoice_no }}</strong>
                with any payment or enquiry.

                @if ($company?->phone)
                    · {{ $company->phone }}
                @endif

                @if ($company?->company_email)
                    · {{ $company->company_email }}
                @endif

                @if ($company?->website)
                    · {{ $company->website }}
                @endif

            </div>

        </div>

    @endif

@endsection
