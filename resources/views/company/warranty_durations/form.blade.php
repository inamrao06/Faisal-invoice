@extends('layouts.app')
@section('title', $duration->exists ? 'Edit Warranty Duration' : 'Add Warranty Duration')
@section('page_title', $duration->exists ? 'Edit Warranty Duration' : 'Add Warranty Duration')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $duration->exists ? route('company.warranty-durations.update', $duration) : route('company.warranty-durations.store') }}">
            @csrf
            @if($duration->exists) @method('PUT') @endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-calendar-time"></i></div>
                    <div>
                        <h6>{{ $duration->exists ? 'Edit Warranty Duration' : 'Add Warranty Duration' }}</h6>
                        <p>Selectable warranty month options used on invoices.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="months">Months <span class="text-danger">*</span></label>
                        <input id="months" name="months" type="number" min="0" max="120"
                               class="form-control @error('months') is-invalid @enderror"
                               value="{{ old('months', $duration->months) }}" required>
                        @error('months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="name">Label</label>
                        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $duration->name) }}" placeholder="e.g. 18 Months">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active', $duration->exists ? $duration->is_active : true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $duration->exists ? 'Update Warranty Duration' : 'Save Warranty Duration' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.warranty-durations.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
