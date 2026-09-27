@extends('layouts.app')
@section('title',$permission->exists?'Edit Permission':'Add Permission')
@section('page_title',$permission->exists?'Edit Permission':'Add Permission')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $permission->exists ? route('company.permissions.update',$permission) : route('company.permissions.store') }}">
            @csrf
            @if($permission->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-lock"></i></div>
                    <div>
                        <h6>{{ $permission->exists ? 'Edit Permission' : 'Add Permission' }}</h6>
                        <p>Choose what a role can do inside a module.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="company_role_id">Role <span class="text-danger">*</span></label>
                        <select id="company_role_id" name="company_role_id"
                                class="form-select @error('company_role_id') is-invalid @enderror" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('company_role_id',$permission->company_role_id)==$role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('company_role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="module_name">Module Name <span class="text-danger">*</span></label>
                        <input id="module_name" name="module_name" class="form-control @error('module_name') is-invalid @enderror"
                               value="{{ old('module_name',$permission->module_name) }}" placeholder="e.g. expenses" required>
                        @error('module_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Allowed Actions</label>
                        <div class="row g-2">
                            <div class="col-6 col-md-3">
                                <label class="perm-tile">
                                    <input type="checkbox" name="can_view" value="1" @checked(old('can_view',$permission->exists?$permission->can_view:true))> View
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="perm-tile">
                                    <input type="checkbox" name="can_create" value="1" @checked(old('can_create',$permission->can_create))> Create
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="perm-tile">
                                    <input type="checkbox" name="can_update" value="1" @checked(old('can_update',$permission->can_update))> Update
                                </label>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="perm-tile">
                                    <input type="checkbox" name="can_delete" value="1" @checked(old('can_delete',$permission->can_delete))> Delete
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $permission->exists ? 'Update Permission' : 'Save Permission' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.permissions.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
