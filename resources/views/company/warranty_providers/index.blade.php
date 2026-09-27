@extends('layouts.app')
@section('title', 'Warranty Providers')
@section('page_title', 'Warranty Providers')
@section('content')
<style>
.wp-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px}
.wp-head h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}.wp-head p{margin:3px 0 0;font-size:13px;color:#64748b}
.wp-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:var(--bs-tertiary-bg);padding:12px 16px}
.wp-table tbody td{padding:13px 16px;vertical-align:middle}
</style>
<div class="wp-head">
    <div><h2>Warranty Providers</h2><p>Manage warranty provider options used on invoices.</p></div>
    <a class="btn btn-primary" href="{{ route('company.warranty-providers.create') }}"><i class="ti ti-plus me-1"></i>Add Provider</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table wp-table align-middle mb-0" data-dx-grid>
            <thead><tr><th>Name</th><th>Type</th><th>Status</th><th class="text-end"></th></tr></thead>
            <tbody>
                @foreach($providers as $provider)
                    <tr>
                        <td class="fw-semibold">{{ $provider->name }}</td>
                        <td>{{ $provider->is_system ? 'Default' : 'Custom' }}</td>
                        <td><span class="status {{ $provider->is_active ? 'on' : 'off' }}">{{ $provider->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('company.warranty-providers.edit', $provider) }}"><i class="ti ti-pencil"></i> Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($providers->hasPages())<div class="p-3 border-top">{{ $providers->links() }}</div>@endif
</div>
@endsection
