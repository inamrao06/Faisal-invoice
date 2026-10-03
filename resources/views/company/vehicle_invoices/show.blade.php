@extends('layouts.app')
@section('title',$invoice->invoice_no)
@section('page_title','Vehicle Sales Invoice')
@section('content')
<div class="invoice-page">
    <div class="invoice-toolbar">
        <div>
            <h2>Vehicle Sales Invoice</h2>
            <p>Company-branded customer invoice with vehicle, warranty, totals, and payments.</p>
        </div>
        <div class="invoice-actions">
            <a class="btn btn-soft-secondary btn-sm" target="_blank" href="{{ route('company.vehicle-invoices.print',$invoice) }}"><i class="ti ti-printer me-1"></i>Print</a>
            <button type="button" class="btn btn-soft-primary btn-sm" data-bs-toggle="modal" data-bs-target="#invoiceEmailModal-{{ $invoice->id }}"><i class="ti ti-mail me-1"></i>Email</button>
            <a class="btn btn-light btn-sm" href="{{ route('company.vehicle-invoices.index') }}">Back</a>
            <a class="btn btn-primary btn-sm" href="{{ route('company.vehicle-invoices.edit',$invoice) }}"><i class="ti ti-pencil me-1"></i>Edit</a>
        </div>
    </div>

    @include('company.vehicle_invoices._sheet', ['invoice' => $invoice])
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
