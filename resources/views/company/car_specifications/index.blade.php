@extends('layouts.app')
@section('title', $title)
@section('page_title', $title)
@section('content')
<div class="spec-head">
    <div><h2>{{ $title }}</h2><p>Manage {{ strtolower($title) }} used in vehicle records and invoices.</p></div>
    <a class="btn btn-primary" href="{{ route('company.car-specifications.create', $type) }}"><i class="ti ti-plus me-1"></i>Add</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table spec-table align-middle mb-0" data-dx-grid>
            <thead><tr><th>Name</th><th>Status</th><th class="text-end"></th></tr></thead>
            <tbody>
                @forelse($specs as $spec)
                    <tr>
                        <td class="fw-semibold">{{ $spec->name }}</td>
                        <td><span class="status {{ $spec->is_active ? 'on' : 'off' }}">{{ $spec->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('company.car-specifications.edit', [$type, $spec]) }}"><i class="ti ti-pencil"></i> Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-5">No records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($specs->hasPages())<div class="p-3 border-top">{{ $specs->links() }}</div>@endif
</div>
@endsection
