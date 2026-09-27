@extends('layouts.app')
@section('title',$category->exists?'Edit Vehicle Category':'Add Vehicle Category')
@section('page_title',$category->exists?'Edit Vehicle Category':'Add Vehicle Category')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $category->exists ? route('company.vehicle-categories.update',$category) : route('company.vehicle-categories.store') }}">
            @csrf
            @if($category->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-category"></i></div>
                    <div>
                        <h6>{{ $category->exists ? 'Edit Vehicle Category' : 'Add Vehicle Category' }}</h6>
                        <p>Sale categories and dynamic invoice terms.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Category --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-info-circle"></i></div>
                    <div>
                        <h6>Category</h6>
                        <p>Basic identification for this vehicle category.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name',$category->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="code">Code <span class="text-danger">*</span></label>
                        <input id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code',$category->code) }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active',$category->exists?$category->is_active:true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Default Warranty --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-shield-check"></i></div>
                    <div>
                        <h6>Default Warranty</h6>
                        <p>Pre-filled onto invoices when this category is selected.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="default_warranty_provider_id">Default Warranty Provider</label>
                        <select id="default_warranty_provider_id" name="default_warranty_provider_id"
                                class="form-select @error('default_warranty_provider_id') is-invalid @enderror">
                            <option value="">None</option>
                            @foreach($providers as $provider)
                                <option value="{{ $provider->id }}" @selected(old('default_warranty_provider_id',$category->default_warranty_provider_id)==$provider->id)>{{ $provider->name }}</option>
                            @endforeach
                        </select>
                        @error('default_warranty_provider_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="default_warranty_months">Default Warranty Months</label>
                        <input id="default_warranty_months" name="default_warranty_months" type="number"
                               class="form-control @error('default_warranty_months') is-invalid @enderror"
                               value="{{ old('default_warranty_months',$category->default_warranty_months) }}">
                        @error('default_warranty_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr class="my-4">

                {{-- Invoice Terms --}}
                <div class="form-section-title">
                    <div class="fs-icon"><i class="ti ti-notes"></i></div>
                    <div>
                        <h6>Invoice Terms</h6>
                        <p>Classification and terms text used on invoices.</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label" for="classification_text">Classification Text</label>
                        <textarea id="classification_text" name="classification_text" rows="2"
                                  class="form-control @error('classification_text') is-invalid @enderror">{{ old('classification_text',$category->classification_text) }}</textarea>
                        @error('classification_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="terms">Terms</label>
                        <textarea id="terms" name="terms" rows="6"
                                  class="form-control @error('terms') is-invalid @enderror">{{ old('terms',$category->terms) }}</textarea>
                        @error('terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $category->exists ? 'Update Category' : 'Save Category' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.vehicle-categories.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
