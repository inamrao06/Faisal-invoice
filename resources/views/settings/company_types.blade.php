@extends('layouts.app')
@section('title', 'Company Types')
@section('page_title', 'Company Types')

@section('content')
<div class="page-actions">
    <div><h2>Company Types</h2><p>Manage names and availability for each company type.</p></div>
</div>

<form class="company-types-panel" method="POST" action="{{ route('settings.company-types.update') }}">
    @csrf
    <div class="company-types-toolbar">
        <label class="company-types-search">
            <i class="ti ti-search" aria-hidden="true"></i>
            <input id="company-type-search" class="form-control" type="search" placeholder="Search company types" aria-label="Search company types">
        </label>
        <div class="company-types-actions">
            <select id="company-type-status" class="form-select" aria-label="Filter company type status" data-toggle="select2" data-placeholder="All statuses">
                <option value="all">All statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Save Types</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0 company-types-table" data-dx-grid>
            <thead><tr><th>Code</th><th>Name</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($types as $type)
                    <tr data-company-type-row>
                        <td><strong>{{ $type->code }}</strong></td>
                        <td><label class="visually-hidden" for="type-name-{{ $type->id }}">{{ $type->code }} name</label><input id="type-name-{{ $type->id }}" class="form-control" name="types[{{ $type->id }}][name]" value="{{ $type->name }}" required maxlength="150"></td>
                        <td><label class="form-check form-switch company-types-switch"><input class="form-check-input" type="checkbox" name="types[{{ $type->id }}][is_active]" value="1" @checked($type->is_active)><span class="company-types-status">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></label></td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-5">No company types configured.</td></tr>
                @endforelse
                <tr id="company-types-no-match" hidden><td colspan="3" class="text-center text-muted py-5">No matching company types.</td></tr>
            </tbody>
        </table>
    </div>
    <div class="company-types-footer"><span id="company-types-count">{{ $types->count() }} company types</span></div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/company-types.js') }}?v=1"></script>
@endpush
