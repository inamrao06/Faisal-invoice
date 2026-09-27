@extends('layouts.app')
@section('title',$method->exists?'Edit Payment Method':'Add Payment Method')
@section('page_title',$method->exists?'Edit Payment Method':'Add Payment Method')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $method->exists ? route('company.payment-methods.update',$method) : route('company.payment-methods.store') }}">
            @csrf
            @if($method->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-credit-card"></i></div>
                    <div>
                        <h6>{{ $method->exists ? 'Edit Payment Method' : 'Add Payment Method' }}</h6>
                        <p>Define a payment account your company can use on expenses.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name',$method->name) }}" placeholder="e.g. Meezan Bank Account" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="code">Code <span class="text-danger">*</span></label>
                        <input id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code',$method->code) }}" placeholder="e.g. meezan_bank" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="2"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Optional notes about this account">{{ old('description',$method->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active',$method->exists?$method->is_active:true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $method->exists ? 'Update Payment Method' : 'Save Payment Method' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.payment-methods.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
