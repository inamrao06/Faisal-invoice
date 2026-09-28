@extends('layouts.app')
@section('title', $expense->invoice_no)
@section('page_title', 'Expense Invoice')
@section('content')
    @php
        $company = $expense->company;
        $currency = $company?->currency?->symbol ?? 'PKR';
        $customTemplate = \App\Models\CompanyDocumentTemplate::defaultFor($expense->branch_id, 'expense');
    @endphp

    

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

        @if($customTemplate)
            {!! $customTemplate->render(\App\Models\CompanyDocumentTemplate::expenseValues($expense)) !!}
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
                        <div class="text-center mt-3">
                            @if($company?->signature_path)
                                <img src="{{ asset('storage/'.$company->signature_path) }}" alt="Signature" style="max-height:58px;max-width:180px;object-fit:contain">
                            @endif
                            @if($company?->stamp_path)
                                <img src="{{ asset('storage/'.$company->stamp_path) }}" alt="Stamp" style="max-height:64px;max-width:110px;object-fit:contain;margin-left:10px">
                            @endif
                            <div class="text-muted fs-12 mt-2">{{ $company?->authorized_person ?: $company?->manager_name ?: 'Authorized Signatory' }}</div>
                            @if($company?->designation)<div class="text-muted fs-12">{{ $company->designation }}</div>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
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
