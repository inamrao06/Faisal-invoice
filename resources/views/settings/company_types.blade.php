@extends('layouts.app')
@section('title','Company Types')
@section('page_title','Company Types')

@section('content')
<div class="row">
    <div class="col-12">
        <form class="card mb-0" method="POST" action="{{ route('settings.company-types.update') }}">
            @csrf
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="app-search">
                        <input id="company-type-search" class="form-control" type="search" placeholder="Search company types..." aria-label="Search company types">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                    <div style="min-width:190px">
                        <label class="visually-hidden" for="company-type-status">Filter by status</label>
                        <select id="company-type-status" class="form-select" data-toggle="select2" data-placeholder="All statuses">
                            <option value="all">All statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary ms-1" type="submit">
                    <i class="ti ti-device-floppy fs-sm me-2"></i>Save Types
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0 company-types-table">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th style="width:1%">Code</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr data-company-type-row>
                                <td><span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">{{ $type->code }}</span></td>
                                <td>
                                    <label class="visually-hidden" for="type-name-{{ $type->id }}">{{ $type->code }} name</label>
                                    <input id="type-name-{{ $type->id }}" class="form-control" name="types[{{ $type->id }}][name]" value="{{ $type->name }}" required maxlength="150">
                                </td>
                                <td>
                                    <label class="form-check form-switch mb-0 d-inline-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" role="switch" name="types[{{ $type->id }}][is_active]" value="1" @checked($type->is_active)>
                                        <span class="company-types-status fs-13">{{ $type->is_active ? 'Active' : 'Inactive' }}</span>
                                    </label>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="table-empty">
                                        <i class="ti ti-building"></i>
                                        <p>No company types configured.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        <tr id="company-types-no-match" hidden>
                            <td colspan="3">
                                <div class="table-empty">
                                    <i class="ti ti-search"></i>
                                    <p>No matching company types.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="card-footer py-2">
                <span class="text-muted fs-12" id="company-types-count">{{ $types->count() }} company types</span>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/company-types.js') }}?v=1"></script>
@endpush
