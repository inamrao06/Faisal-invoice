@extends('layouts.app')
@section('title','Vehicle Categories')
@section('page_title','Vehicle Categories')
@section('content')
<div class="page-actions"><div><h2>Vehicle Categories</h2><p>Editable sale categories and dynamic invoice terms.</p></div><a class="btn btn-primary" href="{{ route('company.vehicle-categories.create') }}"><i class="ti ti-plus"></i> Add Category</a></div>
<div class="panel"><div class="table-responsive"><table class="table mb-0 align-middle" data-dx-grid><thead><tr><th>Name</th><th>Code</th><th>Default Warranty</th><th>Status</th><th></th></tr></thead><tbody>@foreach($categories as $category)<tr><td class="fw-semibold">{{ $category->name }}</td><td>{{ $category->code }}</td><td>{{ $category->default_warranty_months !== null ? $category->default_warranty_months.' months' : '-' }}</td><td><span class="status {{ $category->is_active ? 'on' : 'off' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('company.vehicle-categories.edit',$category) }}">Edit</a></td></tr>@endforeach</tbody></table></div>@if($categories->hasPages())<div class="p-3 border-top">{{ $categories->links() }}</div>@endif</div>
@endsection
