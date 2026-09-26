@extends('layouts.app')
@section('title',$role->exists?'Edit Role':'Add Role')
@section('page_title',$role->exists?'Edit Role':'Add Role')
@section('content')
<style>
.frm-head h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
.frm-head p{margin:3px 0 0;font-size:13px;color:#64748b}
.frm-head{margin-bottom:20px}
.frm-card{background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:var(--bs-border-radius);box-shadow:var(--bs-box-shadow-sm);overflow:hidden;margin-bottom:20px}
.frm-card-body{padding:20px}
.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.form-grid .span-2{grid-column:span 2}
.form-grid label{display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em}
@media(max-width:767.98px){.form-grid{grid-template-columns:1fr}.form-grid .span-2{grid-column:span 1}}
.form-actions{display:flex;gap:10px;align-items:center;padding:16px 20px;border-top:1px solid var(--bs-border-color);background:var(--bs-tertiary-bg)}
</style>

<div class="frm-head">
    <div>
        <h2>{{ $role->exists ? 'Edit Role' : 'Add Role' }}</h2>
        <p>Roles control which modules your company users can access.</p>
    </div>
</div>

<form class="frm-card" method="POST" action="{{ $role->exists ? route('company.roles.update',$role) : route('company.roles.store') }}">
    @csrf
    @if($role->exists)@method('PUT')@endif
    <div class="frm-card-body">
        <div class="form-grid">
            <div>
                <label for="name">Name *</label>
                <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name',$role->name) }}" placeholder="e.g. Branch Manager" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="is_active_lbl">Status</label>
                <div class="form-check form-switch mt-2" id="is_active_lbl">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active',$role->exists?$role->is_active:true))>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
            </div>
            <div class="span-2">
                <label for="description">Description</label>
                <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="2" placeholder="What this role is responsible for">{{ old('description',$role->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>Save</button>
        <a class="btn btn-light" href="{{ route('company.roles.index') }}">Cancel</a>
    </div>
</form>
@endsection
