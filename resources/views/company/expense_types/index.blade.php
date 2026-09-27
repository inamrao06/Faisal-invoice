@extends('layouts.app')
@section('title','Expense Types')
@section('page_title','Expense Types')
@section('content')
<style>
.page-actions h2{font-size:22px;font-weight:800;color:#0f172a}
.page-actions p{font-size:13px;color:#64748b}
.et-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;background:#f8fafc;padding:12px 18px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.et-table tbody td{padding:14px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.et-table tbody tr:last-child td{border-bottom:0}
.et-table tbody tr:hover td{background:#fafbff}
.et-name strong{display:block;font-size:14px;font-weight:700;color:#0f172a}
.et-code{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:700;background:#ede9fe;color:#6d28d9}
.et-empty{padding:56px 24px;text-align:center;color:#94a3b8}
.et-empty i{font-size:44px;display:block;margin-bottom:14px;opacity:.5}
.et-empty p{margin:0;font-size:14px}
</style>

<div class="page-actions">
    <div>
        <h2>Expense Types</h2>
        <p>Company-specific expense categories.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('company.expense-types.create') }}">
        <i class="ti ti-plus"></i> Add Expense Type
    </a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table et-table mb-0 align-middle" data-dx-grid>
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
                @forelse($types as $type)
                    <tr>
                        <td>
                            <div class="et-name">
                                <strong>{{ $type->name }}</strong>
                            </div>
                        </td>
                        <td><span class="et-code"><i class="ti ti-hash"></i>{{ $type->code }}</span></td>
                        <td style="font-size:13px;color:#475569">{{ $type->description ?: '—' }}</td>
                        <td>
                            <span class="status {{ $type->is_active ? 'on' : 'off' }}">
                                {{ $type->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('company.expense-types.edit',$type) }}">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="et-empty">
                                <i class="ti ti-category"></i>
                                <p>No expense types yet.</p>
                                <a href="{{ route('company.expense-types.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus"></i> Add First Expense Type
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($types->hasPages())
        <div class="p-3 border-top">{{ $types->links() }}</div>
    @endif
</div>
@endsection
