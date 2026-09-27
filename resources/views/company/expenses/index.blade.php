@extends('layouts.app')
@section('title','Expenses')
@section('page_title','Expenses')
@section('content')

<div class="page-actions">
    <div>
        <h2>Expenses</h2>
        <p>Create and track all company expenses.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('company.expenses.create') }}">
        <i class="ti ti-plus"></i> Create Expense
    </a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table ex-table mb-0 align-middle" data-dx-grid>
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th class="text-end">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td>
                            <div class="ex-inv">
                                <div class="ex-ic"><i class="ti ti-receipt-2"></i></div>
                                <div>
                                    <strong><a href="{{ route('company.expenses.show',$expense) }}">{{ $expense->invoice_no }}</a></strong>
                                    @if($expense->remarks)<small>{{ str($expense->remarks)->limit(32) }}</small>@endif
                                </div>
                            </div>
                        </td>
                        <td style="font-size:13px;color:#475569;white-space:nowrap">
                            <i class="ti ti-calendar me-1"></i>{{ $expense->expense_date->format('d M Y') }}
                        </td>
                        <td>
                            <span class="ex-tag"><i class="ti ti-tag-filled"></i>{{ $expense->head?->name ?? '—' }}</span>
                        </td>
                        <td>
                            <span class="ex-pay"><i class="ti ti-credit-card"></i>{{ str($expense->payment_method)->replace('_',' ')->title() }}</span>
                        </td>
                        <td>
                            <span class="status {{ in_array($expense->status, ['paid','approved','completed']) ? 'on' : 'off' }}">
                                {{ str($expense->status)->title() }}
                            </span>
                        </td>
                        <td class="text-end">
                            <span class="ex-total">PKR {{ number_format($expense->total_amount,2) }}</span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-light" href="{{ route('company.expenses.show',$expense) }}">
                                <i class="ti ti-eye"></i> View
                            </a>
                            <a class="btn btn-sm btn-outline-primary ms-1" href="{{ route('company.expenses.edit',$expense) }}">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('company.expenses.destroy',$expense) }}" class="d-inline" data-confirm-delete data-invoice="{{ $expense->invoice_no }}">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-light ms-1 text-danger" type="submit" title="Delete expense">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="ex-empty">
                                <i class="ti ti-receipt-off"></i>
                                <p>No expenses recorded yet.</p>
                                <a href="{{ route('company.expenses.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus"></i> Create First Expense
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($expenses->hasPages())
        <div class="p-3 border-top">{{ $expenses->links() }}</div>
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
