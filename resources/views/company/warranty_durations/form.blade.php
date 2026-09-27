@extends('layouts.app')
@section('title', $duration->exists ? 'Edit Warranty Duration' : 'Add Warranty Duration')
@section('page_title', $duration->exists ? 'Edit Warranty Duration' : 'Add Warranty Duration')
@section('content')
<style>
.frm-card{background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:var(--bs-border-radius);box-shadow:var(--bs-box-shadow-sm);overflow:hidden;max-width:760px}
.frm-body{padding:20px}.frm-actions{display:flex;gap:10px;padding:16px 20px;background:var(--bs-tertiary-bg);border-top:1px solid var(--bs-border-color)}
.frm-body label{display:block;font-size:12px;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
</style>
<form class="frm-card" method="POST" action="{{ $duration->exists ? route('company.warranty-durations.update', $duration) : route('company.warranty-durations.store') }}">
    @csrf
    @if($duration->exists) @method('PUT') @endif
    <div class="frm-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label for="months">Months *</label>
                <input id="months" class="form-control @error('months') is-invalid @enderror" type="number" min="0" max="120" name="months" value="{{ old('months', $duration->months) }}" required>
                @error('months')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="name">Label</label>
                <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $duration->name) }}" placeholder="e.g. 18 Months">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $duration->exists ? $duration->is_active : true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
    <div class="frm-actions">
        <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>Save</button>
        <a class="btn btn-light" href="{{ route('company.warranty-durations.index') }}">Cancel</a>
    </div>
</form>
@endsection
