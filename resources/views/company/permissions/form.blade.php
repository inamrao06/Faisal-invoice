@extends('layouts.app')
@section('title',$permission->exists?'Edit Permission':'Add Permission')
@section('page_title',$permission->exists?'Edit Permission':'Add Permission')
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
.perm-box{border:1px solid var(--bs-border-color);border-radius:10px;padding:16px;background:var(--bs-tertiary-bg)}
.perm-box>span{display:block;font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.04em;margin-bottom:12px}
.perm-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
@media(max-width:767.98px){.perm-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
.perm-opt{display:flex;align-items:center;gap:8px;padding:11px 13px;background:var(--bs-body-bg);border:1px solid var(--bs-border-color);border-radius:9px;cursor:pointer;margin:0;text-transform:none;letter-spacing:0;font-size:13px;font-weight:600;color:#0f172a}
.perm-opt:hover{border-color:rgba(var(--bs-primary-rgb),.45)}
.perm-opt input{width:16px;height:16px;accent-color:var(--bs-primary);margin:0;cursor:pointer}
</style>

<div class="frm-head">
    <div>
        <h2>{{ $permission->exists ? 'Edit Permission' : 'Add Permission' }}</h2>
        <p>Choose what a role can do inside a module.</p>
    </div>
</div>

<form class="frm-card" method="POST" action="{{ $permission->exists ? route('company.permissions.update',$permission) : route('company.permissions.store') }}">
    @csrf
    @if($permission->exists)@method('PUT')@endif
    <div class="frm-card-body">
        <div class="form-grid">
            <div>
                <label for="company_role_id">Role *</label>
                <select id="company_role_id" class="form-select @error('company_role_id') is-invalid @enderror" name="company_role_id" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected(old('company_role_id',$permission->company_role_id)==$role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('company_role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div>
                <label for="module_name">Module Name *</label>
                <input id="module_name" class="form-control @error('module_name') is-invalid @enderror" name="module_name" value="{{ old('module_name',$permission->module_name) }}" placeholder="e.g. expenses" required>
                @error('module_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="span-2">
                <div class="perm-box">
                    <span>Allowed Actions</span>
                    <div class="perm-grid">
                        <label class="perm-opt"><input type="checkbox" name="can_view" value="1" @checked(old('can_view',$permission->exists?$permission->can_view:true))> View</label>
                        <label class="perm-opt"><input type="checkbox" name="can_create" value="1" @checked(old('can_create',$permission->can_create))> Create</label>
                        <label class="perm-opt"><input type="checkbox" name="can_update" value="1" @checked(old('can_update',$permission->can_update))> Update</label>
                        <label class="perm-opt"><input type="checkbox" name="can_delete" value="1" @checked(old('can_delete',$permission->can_delete))> Delete</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>Save</button>
        <a class="btn btn-light" href="{{ route('company.permissions.index') }}">Cancel</a>
    </div>
</form>
@endsection
