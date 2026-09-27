@extends('layouts.app')
@section('title', 'Warranty Durations')
@section('page_title', 'Warranty Durations')
@section('content')
<style>
.wd-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px}
.wd-head h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}.wd-head p{margin:3px 0 0;font-size:13px;color:#64748b}
.wd-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#64748b;background:var(--bs-tertiary-bg);padding:12px 16px}
.wd-table tbody td{padding:13px 16px;vertical-align:middle}
</style>
<div class="wd-head">
    <div><h2>Warranty Durations</h2><p>Manage selectable warranty month options used on invoices.</p></div>
    <a class="btn btn-primary" href="{{ route('company.warranty-durations.create') }}"><i class="ti ti-plus me-1"></i>Add Duration</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table wd-table align-middle mb-0" data-dx-grid>
            <thead><tr><th>Name</th><th>Months</th><th>Type</th><th>Status</th><th class="text-end"></th></tr></thead>
            <tbody>
                @foreach($durations as $duration)
                    <tr>
                        <td class="fw-semibold">{{ $duration->name }}</td>
                        <td>{{ $duration->months }}</td>
                        <td>{{ $duration->is_system ? 'Default' : 'Custom' }}</td>
                        <td><span class="status {{ $duration->is_active ? 'on' : 'off' }}">{{ $duration->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('company.warranty-durations.edit', $duration) }}"><i class="ti ti-pencil"></i> Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($durations->hasPages())<div class="p-3 border-top">{{ $durations->links() }}</div>@endif
</div>
@endsection
