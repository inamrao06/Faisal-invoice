@extends('layouts.app')
@section('title','Vehicle Invoices')
@section('page_title','Vehicle Invoices')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search invoices...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('company.vehicle-invoices.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-plus fs-sm me-2"></i>New Invoice
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Vehicle</th>
                            <th>Status</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Balance</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('company.vehicle-invoices.show',$invoice) }}" class="fw-semibold">
                                        {{ $invoice->invoice_no }}
                                    </a>
                                    <p class="cell-sub">{{ $invoice->invoice_date?->format('d M Y') }}</p>
                                </td>
                                <td>{{ $invoice->customer?->name }}</td>
                                <td>
                                    <h6 class="cell-title">{{ $invoice->vehicle?->make_model }}</h6>
                                    <p class="cell-sub">{{ $invoice->vehicle?->registration_no ?: '—' }}</p>
                                </td>
                                <td>
                                    @if($invoice->payment_status === 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">
                                            <i class="ti ti-circle-check me-1"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}
                                        </span>
                                    @elseif($invoice->payment_status === 'part_paid')
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold">
                                            <i class="ti ti-clock me-1"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold">
                                            <i class="ti ti-circle-x me-1"></i>{{ str($invoice->payment_status)->replace('_',' ')->title() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($invoice->total_sale_price,2) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($invoice->balance_amount,2) }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a class="btn btn-soft-primary btn-sm" href="{{ route('company.vehicle-invoices.show',$invoice) }}" title="View">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <a class="btn btn-soft-secondary btn-sm" target="_blank" href="{{ route('company.vehicle-invoices.print',$invoice) }}" title="Print">
                                            <i class="ti ti-printer"></i>
                                        </a>
                                        <button type="button" class="btn btn-soft-success btn-sm" data-bs-toggle="modal" data-bs-target="#invoiceEmailModal-{{ $invoice->id }}" title="Email to customer">
                                            <i class="ti ti-mail"></i>
                                        </button>
                                        <form method="POST" action="{{ route('company.vehicle-invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete invoice {{ $invoice->invoice_no }}? This action cannot be undone from this screen.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-soft-danger btn-sm" title="Delete">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="table-empty">
                                        <i class="ti ti-file-invoice"></i>
                                        <p>No vehicle invoices yet.</p>
                                        <a href="{{ route('company.vehicle-invoices.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>New Invoice
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12" data-panel-count></span>
                @if($invoices->hasPages()){{ $invoices->links() }}@endif
            </div>
        </div>
    </div>
</div>

@foreach($invoices as $invoice)
    @include('company.vehicle_invoices._email_modal', ['invoice' => $invoice])
@endforeach
@endsection
