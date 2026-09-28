@extends('layouts.app')
@section('title','Vehicles')
@section('page_title','Vehicles')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <div class="app-search">
                        <input data-panel-search type="search" class="form-control" placeholder="Search vehicles...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('company.vehicles.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-plus fs-sm me-2"></i>Add Vehicle
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Vehicle</th>
                            <th>Reg</th>
                            <th>VIN</th>
                            <th>Category</th>
                            <th>Specs</th>
                            <th>Status</th>
                            <th class="text-end">Price</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary">
                                                <i class="ti ti-car fs-18"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $vehicle->make_model }}</h6>
                                            <p class="cell-sub">{{ $vehicle->category?->name ?: '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($vehicle->registration_no)
                                        <span class="badge bg-secondary bg-opacity-10 text-body fw-semibold">{{ $vehicle->registration_no }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $vehicle->vin ?: '—' }}</td>
                                <td>{{ $vehicle->category?->name ?: '—' }}</td>
                                <td class="text-muted fs-13">
                                    {{ collect([$vehicle->condition?->name,$vehicle->brand?->name,$vehicle->modelSpec?->name,$vehicle->fuelType?->name,$vehicle->transmissionType?->name])->filter()->join(' / ') ?: '—' }}
                                </td>
                                <td>
                                    @if($vehicle->status === 'reserved')
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold">
                                            <i class="ti ti-clock me-1"></i>{{ str($vehicle->status)->title() }}
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold">
                                            <i class="ti ti-circle-check me-1"></i>{{ str($vehicle->status)->title() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">{{ number_format($vehicle->sale_price,2) }}</td>
                                <td class="text-center">
                                    <a class="btn btn-soft-success btn-sm" href="{{ route('company.expenses.create', ['vehicle_id' => $vehicle->id]) }}" title="Add Expense">
                                        <i class="ti ti-receipt-2"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.vehicles.edit',$vehicle) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="table-empty">
                                        <i class="ti ti-car"></i>
                                        <p>No vehicles saved.</p>
                                        <a href="{{ route('company.vehicles.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-plus me-1"></i>Add Vehicle
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
                @if($vehicles->hasPages()){{ $vehicles->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
