@extends('layouts.app')
@section('title','Customers')
@section('page_title','Customers')
@section('content')
<div class="page-actions"><div><h2>Customers</h2><p>Saved customer details for invoices.</p></div><a class="btn btn-primary" href="{{ route('company.customers.create') }}"><i class="ti ti-plus"></i> Add Customer</a></div>
<div class="panel"><div class="table-responsive"><table class="table mb-0 align-middle" data-dx-grid><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Postcode</th><th></th></tr></thead><tbody>@forelse($customers as $customer)<tr><td class="fw-semibold">{{ $customer->name }}</td><td>{{ $customer->email ?: '-' }}</td><td>{{ $customer->phone ?: '-' }}</td><td>{{ $customer->postcode ?: '-' }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('company.customers.edit',$customer) }}"><i class="ti ti-pencil"></i> Edit</a></td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-5">No customers saved.</td></tr>@endforelse</tbody></table></div>@if($customers->hasPages())<div class="p-3 border-top">{{ $customers->links() }}</div>@endif</div>
@endsection
