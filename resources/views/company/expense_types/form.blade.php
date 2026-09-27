@extends('layouts.app')
@section('title',$type->exists?'Edit Expense Type':'Add Expense Type')
@section('page_title',$type->exists?'Edit Expense Type':'Add Expense Type')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $type->exists ? route('company.expense-types.update',$type) : route('company.expense-types.store') }}">
            @csrf
            @if($type->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-category"></i></div>
                    <div>
                        <h6>{{ $type->exists ? 'Edit Expense Type' : 'Add Expense Type' }}</h6>
                        <p>Categories used when recording company expenses.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name',$type->name) }}" placeholder="e.g. Fuel &amp; Lubricants" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="code">Code <span class="text-danger">*</span></label>
                        <input id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code',$type->code) }}" placeholder="e.g. fuel" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Optional notes about this expense type">{{ old('description',$type->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active',$type->exists ? $type->is_active : true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $type->exists ? 'Update Expense Type' : 'Save Expense Type' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.expense-types.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
