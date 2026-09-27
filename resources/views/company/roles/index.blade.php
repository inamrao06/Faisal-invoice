@extends('layouts.app')
@section('title','Roles')
@section('page_title','Roles')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search roles...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('company.roles.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-plus fs-sm me-2"></i>Add Role
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary">
                                                <i class="ti ti-shield-half fs-18"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $role->name }}</h6>
                                            <p class="cell-sub">Company user role</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $role->description ?: '—' }}</td>
                                <td>
                                    @if($role->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.roles.edit',$role) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="table-empty">
                                        <i class="ti ti-shield-off"></i>
                                        <p>No roles yet.</p>
                                        <a href="{{ route('company.roles.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>Add Role
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12" data-panel-count></span>
                @if($roles->hasPages()){{ $roles->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
