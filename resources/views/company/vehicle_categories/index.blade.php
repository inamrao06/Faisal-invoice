@extends('layouts.app')
@section('title','Vehicle Categories')
@section('page_title','Vehicle Categories')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search categories...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('company.vehicle-categories.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-plus fs-sm me-2"></i>Add Category
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Name</th>
                            <th>Code</th>
                            <th>Default Warranty</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary">
                                                <i class="ti ti-category fs-18"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $category->name }}</h6>
                                            <p class="cell-sub">{{ $category->created_at?->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">{{ $category->code }}</span></td>
                                <td class="text-muted">{{ $category->default_warranty_months !== null ? $category->default_warranty_months.' months' : '—' }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.vehicle-categories.edit',$category) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="table-empty">
                                        <i class="ti ti-category"></i>
                                        <p>No vehicle categories yet.</p>
                                        <a href="{{ route('company.vehicle-categories.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>Add Category
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
                @if($categories->hasPages()){{ $categories->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
