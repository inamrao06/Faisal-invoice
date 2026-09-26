@extends('layouts.app')
@section('title','Payment Methods')
@section('page_title','Payment Methods')
@section('content')
<style>
.page-actions h2{font-size:22px;font-weight:800;color:#0f172a}
.page-actions p{font-size:13px;color:#64748b}
.pm-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;background:#f8fafc;padding:12px 18px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.pm-table tbody td{padding:14px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.pm-table tbody tr:last-child td{border-bottom:0}
.pm-table tbody tr:hover td{background:#fafbff}
.pm-name strong{display:block;font-size:14px;font-weight:700;color:#0f172a}
.pm-name small{display:block;font-size:12px;color:#64748b}
.pm-code{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:700;background:#ede9fe;color:#6d28d9}
.pm-empty{padding:56px 24px;text-align:center;color:#94a3b8}
.pm-empty i{font-size:44px;display:block;margin-bottom:14px;opacity:.5}
.pm-empty p{margin:0;font-size:14px}
</style>

<div class="page-actions">
    <div>
        <h2>Payment Methods</h2>
        <p>Payment accounts available when recording expenses.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('company.payment-methods.create') }}">
        <i class="ti ti-plus"></i> Add Payment Method
    </a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table pm-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($methods as $method)
                    <tr>
                        <td>
                            <div class="pm-name">
                                <strong>{{ $method->name }}</strong>
                            </div>
                        </td>
                        <td><span class="pm-code"><i class="ti ti-hash"></i>{{ $method->code }}</span></td>
                        <td style="font-size:13px;color:#475569">{{ $method->description ?: '—' }}</td>
                        <td>
                            <span class="status {{ $method->is_active ? 'on' : 'off' }}">
                                {{ $method->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('company.payment-methods.edit',$method) }}">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="pm-empty">
                                <i class="ti ti-credit-card"></i>
                                <p>No payment methods yet.</p>
                                <a href="{{ route('company.payment-methods.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus"></i> Add First Payment Method
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($methods->hasPages())
        <div class="p-3 border-top">{{ $methods->links() }}</div>
    @endif
</div>
@endsection
