@extends('layouts.app')
@section('title','Companies')
@section('page_title','Companies')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search companies...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                @can('manage-branches')
                    <div class="d-flex align-items-center gap-1">
                        <a href="{{ route('companies.create') }}" class="btn btn-primary ms-1">
                            <i class="ti ti-plus fs-sm me-2"></i>Add Company
                        </a>
                    </div>
                @endcan
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Company</th>
                            <th>Type</th>
                            <th>Contact</th>
                            <th>Currency</th>
                            <th class="text-center">Users</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($companies as $company)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="co-logo me-2">
                                            @if($company->logo_path)
                                                <img src="{{ asset('storage/'.$company->logo_path) }}" alt="{{ $company->name }}">
                                            @else
                                                <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary fw-semibold">
                                                    {{ strtoupper(substr($company->name,0,1)) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $company->name }}</h6>
                                            <p class="cell-sub">
                                                {{ $company->code }}
                                                @if($company->company_email) &middot; {{ $company->company_email }} @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">
                                        <i class="ti ti-tag me-1"></i>{{ str($company->type)->replace('_',' ')->title() }}
                                    </span>
                                </td>
                                <td>
                                    <p class="cell-sub mb-0">
                                        @if($company->phone)<i class="ti ti-phone me-1"></i>{{ $company->phone }}@endif
                                    </p>
                                    @if($company->city)
                                        <p class="cell-sub mb-0"><i class="ti ti-map-pin me-1"></i>{{ $company->city }}</p>
                                    @endif
                                    @if(!$company->phone && !$company->city)
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if($company->currency)
                                        <h6 class="cell-title">{{ $company->currency->code }}</h6>
                                        <p class="cell-sub">{{ $company->currency->symbol }}</p>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info fw-semibold">{{ $company->users_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($company->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <a class="btn btn-soft-secondary btn-sm" href="{{ route('companies.show',$company) }}" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    @can('manage-branches')
                                        <a class="btn btn-soft-primary btn-sm" href="{{ route('companies.edit',$company) }}" title="Edit">
                                            <i class="ti ti-pencil"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="table-empty">
                                        <i class="ti ti-buildings"></i>
                                        <p>No companies registered yet.</p>
                                        @can('manage-branches')
                                            <a href="{{ route('companies.create') }}" class="btn btn-primary mt-3">
                                                <i class="ti ti-plus me-1"></i>Create First Company
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12" data-panel-count></span>
                @if($companies->hasPages()){{ $companies->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
