@extends('layouts.app')
@section('title', $expense->invoice_no)
@section('page_title', 'Expense Invoice')
@section('content')
    @php
        $company = $expense->company;
        $currency = $company?->currency?->symbol ?? 'PKR';
    @endphp

    <style>
        .invoice-page{max-width:1120px;margin:0 auto}
        .invoice-toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:16px}
        .invoice-toolbar h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
        .invoice-toolbar p{margin:3px 0 0;font-size:13px;color:#64748b}
        .invoice-actions{display:flex;gap:8px;flex-wrap:wrap}
        .invoice-sheet{background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:10px;box-shadow:var(--bs-box-shadow-sm);overflow:hidden}
        .invoice-hero{padding:28px 30px 22px;display:grid;grid-template-columns:minmax(0,1fr) 280px;gap:28px;border-bottom:1px solid var(--bs-border-color)}
        .brand-row{display:flex;gap:14px;align-items:flex-start}
        .brand-mark{width:58px;height:58px;border-radius:10px;background:rgba(var(--bs-primary-rgb),.1);display:grid;place-items:center;overflow:hidden;flex:0 0 58px;color:var(--bs-primary);font-weight:900;font-size:24px}
        .brand-mark img{width:100%;height:100%;object-fit:contain;padding:5px}
        .brand-copy h3{margin:0 0 6px;font-size:20px;font-weight:800;color:#0f172a}
        .brand-copy p{margin:0 0 3px;color:#64748b;font-size:13px}
        .invoice-title{text-align:right}
        .invoice-title span{display:block;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8}
        .invoice-title strong{display:block;font-size:26px;color:#0f172a;line-height:1.2;margin:4px 0 10px}
        .status-pill{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 11px;background:#dcfce7;color:#15803d;font-size:12px;font-weight:800}
        .invoice-meta-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));border-bottom:1px solid var(--bs-border-color);background:var(--bs-tertiary-bg)}
        .meta-item{padding:15px 18px;border-right:1px solid var(--bs-border-color)}
        .meta-item:last-child{border-right:0}
        .meta-item span{display:block;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:5px}
        .meta-item strong{display:block;font-size:14px;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .invoice-body{padding:24px 30px}
        .section-label{font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:10px}
        .items-card{border:1px solid var(--bs-border-color);border-radius:8px;overflow:hidden}
        .invoice-table{margin:0}
        .invoice-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:#f8fafc;padding:12px 14px;white-space:nowrap;border-bottom:1px solid var(--bs-border-color)}
        .invoice-table tbody td{padding:13px 14px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
        .invoice-table tbody tr:last-child td{border-bottom:0}
        .money{font-weight:800;color:#0f172a;white-space:nowrap}
        .invoice-bottom{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:24px;margin-top:22px;align-items:start}
        .info-box{border:1px solid var(--bs-border-color);border-radius:8px;padding:15px;background:var(--bs-body-bg)}
        .info-box + .info-box{margin-top:12px}
        .info-box span{display:block;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:7px}
        .info-box p{margin:0;color:#475569}
        .total-card{border:1px solid var(--bs-border-color);border-radius:8px;background:var(--bs-tertiary-bg);padding:16px}
        .total-row{display:flex;justify-content:space-between;gap:18px;padding:8px 0;color:#475569}
        .total-row:not(:last-child){border-bottom:1px dashed var(--bs-border-color)}
        .total-row.grand{font-size:20px;font-weight:900;color:#0f172a;padding-top:12px}
        @media(max-width:900px){.invoice-hero{grid-template-columns:1fr}.invoice-title{text-align:left}.invoice-meta-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.meta-item:nth-child(2){border-right:0}.meta-item{border-bottom:1px solid var(--bs-border-color)}.invoice-bottom{grid-template-columns:1fr}}
        @media(max-width:575.98px){.invoice-hero,.invoice-body{padding:20px}.invoice-meta-grid{grid-template-columns:1fr}.meta-item{border-right:0}.invoice-actions{width:100%}.invoice-actions .btn,.invoice-actions form{flex:1}.invoice-actions form .btn{width:100%}.brand-row{flex-direction:column}}
    </style>

    <div class="invoice-page">
        <div class="invoice-toolbar">
            <div>
                <h2>Expense Invoice</h2>
                <p>Review the expense record, line items, totals, and attachments.</p>
            </div>
            <div class="invoice-actions">
                <a class="btn btn-light" href="{{ route('company.expenses.index') }}"><i class="ti ti-arrow-left me-1"></i>Back</a>
                <a class="btn btn-primary" href="{{ route('company.expenses.edit', $expense) }}"><i class="ti ti-pencil me-1"></i>Edit</a>
                <form method="POST" action="{{ route('company.expenses.destroy', $expense) }}" data-confirm-delete data-invoice="{{ $expense->invoice_no }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-light text-danger" type="submit"><i class="ti ti-trash me-1"></i>Delete</button>
                </form>
            </div>
        </div>

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
                        @if($company?->address || $company?->city)<p><i class="ti ti-map-pin me-1"></i>{{ trim(($company->address ?? '') . ' ' . ($company->city ?? '')) }}</p>@endif
                    </div>
                </div>
                <div class="invoice-title">
                    <span>Invoice No</span>
                    <strong>{{ $expense->invoice_no }}</strong>
                    <span class="status-pill"><i class="ti ti-circle-check"></i>{{ str($expense->status)->title() }}</span>
                </div>
            </div>

            <div class="invoice-meta-grid">
                <div class="meta-item"><span>Expense Date</span><strong>{{ $expense->expense_date->format('d M Y') }}</strong></div>
                <div class="meta-item"><span>Expense Type</span><strong>{{ $expense->head?->name ?? '-' }}</strong></div>
                <div class="meta-item"><span>Payment Method</span><strong>{{ str($expense->payment_method)->replace('_', ' ')->title() }}</strong></div>
                <div class="meta-item"><span>Created</span><strong>{{ $expense->created_at?->format('d M Y') }}</strong></div>
            </div>

            <div class="invoice-body">
                <div class="section-label">Line Items</div>
                <div class="items-card table-responsive">
                    <table class="table invoice-table align-middle">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Rate</th>
                                <th class="text-end">Tax</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expense->items as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->description }}</td>
                                    <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="text-end">{{ $currency }} {{ number_format($item->rate, 2) }}</td>
                                    <td class="text-end">{{ number_format($item->tax_percent, 2) }}%</td>
                                    <td class="text-end money">{{ $currency }} {{ number_format($item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="invoice-bottom">
                    <div>
                        <div class="info-box">
                            <span>Remarks</span>
                            <p>{{ $expense->remarks ?: 'No remarks added.' }}</p>
                        </div>
                        <div class="info-box">
                            <span>Attachment</span>
                            @if($expense->attachment_path)
                                <a href="{{ asset('storage/'.$expense->attachment_path) }}" target="_blank" rel="noopener"><i class="ti ti-paperclip me-1"></i>View attachment</a>
                            @else
                                <p class="text-muted">No attachment uploaded.</p>
                            @endif
                        </div>
                        <div class="info-box">
                            <span>Warranty</span>
                            <p>
                                <strong>{{ $expense->warranty_provider_name ?? $expense->warrantyProvider?->name ?? 'No Warranty' }}</strong>
                                @if($expense->warranty_duration_months !== null)
                                    <br>{{ $expense->warranty_duration_months }} month{{ (int) $expense->warranty_duration_months === 1 ? '' : 's' }}
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="total-card">
                        <div class="total-row"><span>Subtotal</span><strong>{{ $currency }} {{ number_format($expense->subtotal, 2) }}</strong></div>
                        <div class="total-row"><span>Total Tax</span><strong>{{ $currency }} {{ number_format($expense->tax_amount, 2) }}</strong></div>
                        <div class="total-row grand"><span>Grand Total</span><strong>{{ $currency }} {{ number_format($expense->total_amount, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('paces/assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script src="{{ asset('paces/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        document.querySelectorAll('[data-confirm-delete]').forEach(function(form) {
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                var invoice = form.dataset.invoice || 'this expense';
                if (!window.Swal) {
                    if (window.confirm('Delete ' + invoice + '?')) form.submit();
                    return;
                }
                var result = await Swal.fire({
                    title: 'Delete expense?',
                    text: invoice + ' will be removed from the expense list.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    reverseButtons: true,
                    focusCancel: true
                });
                if (result.isConfirmed) form.submit();
            });
        });
    </script>
@endpush
