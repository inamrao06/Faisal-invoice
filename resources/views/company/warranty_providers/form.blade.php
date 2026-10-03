@extends('layouts.app')
@section('title', $provider->exists ? 'Edit Warranty Provider' : 'Add Warranty Provider')
@section('page_title', $provider->exists ? 'Edit Warranty Provider' : 'Add Warranty Provider')
@section('content')
    <div class="row">
        <div class="col-xl-8">
            <form class="card form-card mb-0" method="POST"
                action="{{ $provider->exists ? route('company.warranty-providers.update', $provider) : route('company.warranty-providers.store') }}">
                @csrf
                @if ($provider->exists)
                    @method('PUT')
                @endif

                <div class="card-header border-light">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-shield-check"></i></div>
                        <div>
                            <h6>{{ $provider->exists ? 'Edit Warranty Provider' : 'Add Warranty Provider' }}</h6>
                            <p>Warranty provider options used on invoices.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label" for="name">Provider Name <span
                                    class="text-danger">*</span></label>
                            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $provider->name) }}" placeholder="e.g. Handler Protect" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                          <div class="col-md-12">
                            <label class="form-label" for="description">Description <span
                                    class="text-danger">*</span></label>
                            <textarea id="description" name="description" rows="3"
                                class="form-control @error('description') is-invalid @enderror" placeholder="e.g. Handler Protect"
                                required>{{ old('description', $provider->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-12">
                            <label class="form-label d-block">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                                    name="is_active" value="1" @checked(old('is_active', $provider->exists ? $provider->is_active : true))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i
                            class="ti ti-check me-1"></i>{{ $provider->exists ? 'Update Warranty Provider' : 'Save Warranty Provider' }}
                    </button>
                    <a class="btn btn-light" href="{{ route('company.warranty-providers.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
