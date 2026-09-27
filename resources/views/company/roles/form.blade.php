@extends('layouts.app')
@section('title',$role->exists?'Edit Role':'Add Role')
@section('page_title',$role->exists?'Edit Role':'Add Role')
@section('content')
<div class="row">
    <div class="col-xl-8">
        <form class="card form-card mb-0" method="POST"
              action="{{ $role->exists ? route('company.roles.update',$role) : route('company.roles.store') }}">
            @csrf
            @if($role->exists)@method('PUT')@endif

            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-shield-half"></i></div>
                    <div>
                        <h6>{{ $role->exists ? 'Edit Role' : 'Add Role' }}</h6>
                        <p>Roles control which modules your company users can access.</p>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                        <input id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name',$role->name) }}" placeholder="e.g. Branch Manager" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description" rows="2"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="What this role is responsible for">{{ old('description',$role->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active"
                                   value="1" @checked(old('is_active',$role->exists?$role->is_active:true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="ti ti-check me-1"></i>{{ $role->exists ? 'Update Role' : 'Save Role' }}
                </button>
                <a class="btn btn-light" href="{{ route('company.roles.index') }}">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
