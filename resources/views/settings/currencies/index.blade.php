@extends('layouts.app')
@section('title','Currencies')
@section('page_title','Currency')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search currencies...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('settings.currencies.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-plus fs-sm me-2"></i>Add Currency
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Code</th>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th class="text-center">Default</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($currencies as $currency)
                            <tr>
                                <td><span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">{{ $currency->code }}</span></td>
                                <td><h6 class="cell-title">{{ $currency->name }}</h6></td>
                                <td class="fw-semibold">{{ $currency->symbol }}</td>
                                <td class="text-center">
                                    @if($currency->is_default)
                                        <span class="badge bg-info bg-opacity-10 text-info fw-semibold"><i class="ti ti-star-filled me-1"></i>Default</span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if($currency->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('settings.currencies.edit',$currency) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="table-empty">
                                        <i class="ti ti-currency"></i>
                                        <p>No currencies yet.</p>
                                        <a href="{{ route('settings.currencies.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>Add Currency
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
                @if($currencies->hasPages()){{ $currencies->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
