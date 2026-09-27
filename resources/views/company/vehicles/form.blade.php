@extends('layouts.app')
@section('title',$vehicle->exists?'Edit Vehicle':'Add Vehicle')
@section('page_title',$vehicle->exists?'Edit Vehicle':'Add Vehicle')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $vehicle->exists ? route('company.vehicles.update',$vehicle) : route('company.vehicles.store') }}">
            @csrf
            @if($vehicle->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-car"></i></div>
                    <div>
                        <h6>{{ $vehicle->exists ? 'Edit Vehicle' : 'Add Vehicle' }}</h6>
                        <p>Stock vehicles linked to invoices and expenses.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Vehicle Details --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-info-circle"></i></div>
                    <div>
                        <h6>Vehicle Details</h6>
                        <p>Core identifiers, specs and pricing.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="make_model">Make / Model <span class="text-danger">*</span></label>
                        <input id="make_model" name="make_model" class="form-control @error('make_model') is-invalid @enderror"
                               value="{{ old('make_model',$vehicle->make_model) }}" required>
                        @error('make_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="registration_no">Registration</label>
                        <input id="registration_no" name="registration_no" class="form-control @error('registration_no') is-invalid @enderror"
                               value="{{ old('registration_no',$vehicle->registration_no) }}">
                        @error('registration_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="vin">VIN / Chassis</label>
                        <input id="vin" name="vin" class="form-control @error('vin') is-invalid @enderror"
                               value="{{ old('vin',$vehicle->vin) }}">
                        @error('vin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="year">Year</label>
                        <input id="year" name="year" type="number" class="form-control @error('year') is-invalid @enderror"
                               value="{{ old('year',$vehicle->year) }}">
                        @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="mileage">Mileage</label>
                        <input id="mileage" name="mileage" type="number" class="form-control @error('mileage') is-invalid @enderror"
                               value="{{ old('mileage',$vehicle->mileage) }}">
                        @error('mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="keys_count">Keys</label>
                        <input id="keys_count" name="keys_count" type="number" class="form-control @error('keys_count') is-invalid @enderror"
                               value="{{ old('keys_count',$vehicle->keys_count) }}">
                        @error('keys_count')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="sale_price">Sale Price</label>
                        <input id="sale_price" name="sale_price" type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror"
                               value="{{ old('sale_price',$vehicle->sale_price) }}">
                        @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="status">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['available','reserved','sold'] as $s)
                                <option value="{{ $s }}" @selected(old('status',$vehicle->status)===$s)>{{ str($s)->title() }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Classification --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-category"></i></div>
                    <div>
                        <h6>Classification</h6>
                        <p>Category and specification lookups.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="vehicle_category_id">Category</label>
                        <select id="vehicle_category_id" name="vehicle_category_id" class="form-select @error('vehicle_category_id') is-invalid @enderror">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('vehicle_category_id',$vehicle->vehicle_category_id)==$category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('vehicle_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="condition_id">Condition</label>
                        <select id="condition_id" name="condition_id" class="form-select @error('condition_id') is-invalid @enderror">
                            <option value="">Select condition</option>
                            @foreach($conditions as $spec)
                                <option value="{{ $spec->id }}" @selected(old('condition_id',$vehicle->condition_id)==$spec->id)>{{ $spec->name }}</option>
                            @endforeach
                        </select>
                        @error('condition_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="brand_id">Brand</label>
                        <select id="brand_id" name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">Select brand</option>
                            @foreach($brands as $spec)
                                <option value="{{ $spec->id }}" @selected(old('brand_id',$vehicle->brand_id)==$spec->id)>{{ $spec->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="model_id">Model</label>
                        <select id="model_id" name="model_id" class="form-select @error('model_id') is-invalid @enderror">
                            <option value="">Select model</option>
                            @foreach($models as $spec)
                                <option value="{{ $spec->id }}" @selected(old('model_id',$vehicle->model_id)==$spec->id)>{{ $spec->name }}</option>
                            @endforeach
                        </select>
                        @error('model_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="fuel_type_id">Fuel Type</label>
                        <select id="fuel_type_id" name="fuel_type_id" class="form-select @error('fuel_type_id') is-invalid @enderror">
                            <option value="">Select fuel</option>
                            @foreach($fuelTypes as $spec)
                                <option value="{{ $spec->id }}" @selected(old('fuel_type_id',$vehicle->fuel_type_id)==$spec->id)>{{ $spec->name }}</option>
                            @endforeach
                        </select>
                        @error('fuel_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="transmission_type_id">Transmission</label>
                        <select id="transmission_type_id" name="transmission_type_id" class="form-select @error('transmission_type_id') is-invalid @enderror">
                            <option value="">Select transmission</option>
                            @foreach($transmissionTypes as $spec)
                                <option value="{{ $spec->id }}" @selected(old('transmission_type_id',$vehicle->transmission_type_id)==$spec->id)>{{ $spec->name }}</option>
                            @endforeach
                        </select>
                        @error('transmission_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $vehicle->exists ? 'Update Vehicle' : 'Save Vehicle' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.vehicles.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
