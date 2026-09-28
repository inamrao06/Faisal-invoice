@extends('layouts.app')
@section('title','Templates')
@section('page_title','Templates')
@section('content')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-template"></i></div>
                    <div>
                        <h6>Document Templates</h6>
                        <p>Create invoice and expense templates for this company.</p>
                    </div>
                </div>
                <a href="{{ route('company.templates.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Create Template</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom table-centered mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>
                                    <h6 class="cell-title">{{ $template->name }}</h6>
                                    @if($template->is_default)<p class="cell-sub">Default template</p>@endif
                                </td>
                                <td>{{ str($template->type)->replace('_',' ')->title() }}</td>
                                <td>
                                    <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 {{ $template->is_active ? 'text-success' : 'text-secondary' }}">
                                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.templates.edit',$template) }}" title="Edit"><i class="ti ti-pencil"></i></a>
                                    <form method="POST" action="{{ route('company.templates.destroy',$template) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-soft-danger btn-sm" type="submit" title="Delete"><i class="ti ti-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="table-empty">
                                        <i class="ti ti-template"></i>
                                        <p>No templates created yet.</p>
                                        <a href="{{ route('company.templates.create') }}" class="btn btn-primary mt-3"><i class="ti ti-plus me-1"></i>Create Template</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-3">
            <div class="card-header border-light"><h6 class="mb-0">Template Types</h6></div>
            <div class="card-body d-grid gap-2">
                @foreach($types as $key => $type)
                    <div class="info-strip"><p class="text-muted fs-12 text-uppercase mb-1">Template {{ $key }}</p><h6 class="mb-0">{{ str($type)->replace('_',' ')->title() }}</h6></div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header border-light"><h6 class="mb-0">Company Tags</h6></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @foreach($companyTags as $tag => $value)
                        <div><code>{{ $tag }}</code><small class="text-muted d-block">{{ filled($value) ? $value : '-' }}</small></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
