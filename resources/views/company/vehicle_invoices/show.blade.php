@extends('layouts.app')
@section('title',$invoice->invoice_no)
@section('page_title','Vehicle Sales Invoice')
@section('content')
@php
    $company = $invoice->company;
    $currency = $company?->currency?->symbol ?? 'PKR';
    $money = fn ($value) => $currency . ' ' . number_format((float) $value, 2);
    $vehicle = $invoice->vehicle;
    $customer = $invoice->customer;
    $statusClass = match ($invoice->payment_status) {
        'paid' => 'success',
        'part_paid', 'deposit' => 'warning',
        default => 'danger',
    };
@endphp

@push('styles')
<style>
    .invoice-show-shell {
        display: grid;
        gap: 18px;
    }

    .invoice-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 18px;
        align-items: center;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .invoice-hero h2 {
        margin: 0;
        color: #0f172a;
        font-size: 22px;
        font-weight: 900;
    }

    .invoice-hero p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .invoice-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
    }

    .invoice-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .invoice-summary-card,
    .invoice-info-card {
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .invoice-summary-card {
        padding: 14px;
    }

    .invoice-summary-card span,
    .invoice-info-card span {
        display: block;
        margin-bottom: 4px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .invoice-summary-card strong {
        display: block;
        color: #0f172a;
        font-size: 17px;
        font-weight: 900;
    }

    .invoice-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .invoice-info-card .head {
        padding: 10px 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #0f172a;
        font-weight: 900;
    }

    .invoice-info-card .body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 14px;
    }

    .invoice-info-card strong {
        display: block;
        color: #0f172a;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .invoice-sheet-frame {
        overflow-x: auto;
    }

    @media (max-width: 991px) {
        .invoice-hero,
        .invoice-info-grid {
            grid-template-columns: 1fr;
        }

        .invoice-actions {
            justify-content: flex-start;
        }

        .invoice-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575px) {
        .invoice-summary-grid,
        .invoice-info-card .body {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

<div class="invoice-page">
    <div class="invoice-show-shell">
    <div class="invoice-hero">
        <div>
            <h2>{{ $invoice->invoice_no }}</h2>
            <p>{{ $customer?->name ?? 'Customer' }} - {{ $vehicle?->make_model ?? 'Vehicle' }} - {{ $invoice->invoice_date?->format('d M Y') }}</p>
        </div>
        <div class="invoice-actions">
            <span class="badge bg-{{ $statusClass }} align-self-center">{{ str($invoice->payment_status)->replace('_',' ')->title() }}</span>
            <a class="btn btn-soft-secondary btn-sm" target="_blank" href="{{ route('company.vehicle-invoices.print',$invoice) }}"><i class="ti ti-printer me-1"></i>Print</a>
            <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#invoiceEmailModal-{{ $invoice->id }}"><i class="ti ti-mail me-1"></i>Email</button>
            <a class="btn btn-light btn-sm" href="{{ route('company.vehicle-invoices.index') }}">Back</a>
            <a class="btn btn-primary btn-sm" href="{{ route('company.vehicle-invoices.edit',$invoice) }}"><i class="ti ti-pencil me-1"></i>Edit</a>
            <form method="POST" action="{{ route('company.vehicle-invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete invoice {{ $invoice->invoice_no }}? This action cannot be undone from this screen.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash me-1"></i>Delete</button>
            </form>
        </div>
    </div>

    <div class="invoice-summary-grid">
        <div class="invoice-summary-card">
            <span>Total Sale</span>
            <strong>{{ $money($invoice->total_sale_price) }}</strong>
        </div>
        <div class="invoice-summary-card">
            <span>Total Paid</span>
            <strong>{{ $money($invoice->total_paid) }}</strong>
        </div>
        <div class="invoice-summary-card">
            <span>Balance Due</span>
            <strong>{{ $money($invoice->balance_amount) }}</strong>
        </div>
        <div class="invoice-summary-card">
            <span>Payments</span>
            <strong>{{ $invoice->payments->count() }}</strong>
        </div>
    </div>

    <div class="invoice-info-grid">
        <div class="invoice-info-card">
            <div class="head">Customer Information</div>
            <div class="body">
                <div><span>Name</span><strong>{{ $customer?->name ?? '-' }}</strong></div>
                <div><span>Phone</span><strong>{{ $customer?->phone ?? '-' }}</strong></div>
                <div><span>Email</span><strong>{{ $customer?->email ?? '-' }}</strong></div>
                <div><span>Address</span><strong>{{ trim(($customer?->address ?? '').' '.($customer?->postcode ?? '')) ?: '-' }}</strong></div>
            </div>
        </div>
        <div class="invoice-info-card">
            <div class="head">Car Information</div>
            <div class="body">
                <div><span>Vehicle</span><strong>{{ $vehicle?->make_model ?? '-' }}</strong></div>
                <div><span>Registration</span><strong>{{ $vehicle?->registration_no ?? '-' }}</strong></div>
                <div><span>VIN</span><strong>{{ $vehicle?->vin ?? '-' }}</strong></div>
                <div><span>Category</span><strong>{{ $invoice->category?->name ?? $vehicle?->category?->name ?? '-' }}</strong></div>
                <div><span>Year</span><strong>{{ $vehicle?->year ?? '-' }}</strong></div>
                <div><span>Mileage</span><strong>{{ $vehicle?->mileage !== null ? number_format((float) $vehicle->mileage) : '-' }}</strong></div>
            </div>
        </div>
    </div>

    <div class="invoice-sheet-frame">
        @include('company.vehicle_invoices._sheet', ['invoice' => $invoice])
    </div>
    </div>
</div>

@include('company.vehicle_invoices._email_modal', ['invoice' => $invoice])
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($errors->any())
            var emailModal = document.getElementById('invoiceEmailModal-{{ $invoice->id }}');
            if (emailModal) { bootstrap.Modal.getOrCreateInstance(emailModal).show(); }
        @endif
    });
</script>
@endpush
