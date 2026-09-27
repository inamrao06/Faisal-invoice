@extends('layouts.app')
@section('title', $spec->exists ? 'Edit '.$title : 'Add '.$title)
@section('page_title', $spec->exists ? 'Edit '.$title : 'Add '.$title)
@section('content')
<style>
.frm-card{background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:var(--bs-border-radius);box-shadow:var(--bs-box-shadow-sm);overflow:hidden;max-width:760px}
.frm-body{padding:20px}.frm-actions{display:flex;gap:10px;padding:16px 20px;background:var(--bs-tertiary-bg);border-top:1px solid var(--bs-border-color)}
.frm-body label{display:block;font-size:12px;font-weight:800;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
</style>
<form class="frm-card" method="POST" action="{{ $spec->exists ? route('company.car-specifications.update', [$type, $spec]) : route('company.car-specifications.store', $type) }}">
    @csrf
    @if($spec->exists) @method('PUT') @endif
    <div class="frm-body">
        <label for="name">Name *</label>
        <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $spec->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $spec->exists ? $spec->is_active : true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>
    </div>
    <div class="frm-actions">
        <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>Save</button>
        <a class="btn btn-light" href="{{ route('company.car-specifications.index', $type) }}">Cancel</a>
    </div>
</form>
@endsection
