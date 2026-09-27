@extends('layouts.app')
@section('title','Permissions')
@section('page_title','Permissions')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search permissions...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('company.permissions.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-plus fs-sm me-2"></i>Add Permission
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Role</th>
                            <th>Module</th>
                            <th class="text-center">Create</th>
                            <th class="text-center">Update</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Delete</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary">
                                                <i class="ti ti-shield-half fs-18"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $permission->role?->name ?? '—' }}</h6>
                                            <p class="cell-sub">Role permissions</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">{{ $permission->module_name }}</span></td>
                                <td class="text-center">
                                    @if($permission->can_create)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">Yes</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-muted fw-semibold">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($permission->can_update)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">Yes</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-muted fw-semibold">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($permission->can_view)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">Yes</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-muted fw-semibold">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($permission->can_delete)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">Yes</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-muted fw-semibold">No</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.permissions.edit',$permission) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="table-empty">
                                        <i class="ti ti-lock"></i>
                                        <p>No permissions defined yet.</p>
                                        <a href="{{ route('company.permissions.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>Add Permission
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
                @if($permissions->hasPages()){{ $permissions->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
