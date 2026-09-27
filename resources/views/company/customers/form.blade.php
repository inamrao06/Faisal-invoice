@extends('layouts.app')
@section('title',$customer->exists?'Edit Customer':'Add Customer')
@section('page_title',$customer->exists?'Edit Customer':'Add Customer')
@section('content')
<form class="panel form-panel" method="POST" action="{{ $customer->exists ? route('company.customers.update',$customer) : route('company.customers.store') }}">@csrf @if($customer->exists)@method('PUT')@endif
<div class="form-grid"><div><label>Name *</label><input class="form-control" name="name" value="{{ old('name',$customer->name) }}" required></div><div><label>Email</label><input class="form-control" type="email" name="email" value="{{ old('email',$customer->email) }}"></div><div><label>Phone</label><input class="form-control" name="phone" value="{{ old('phone',$customer->phone) }}"></div><div><label>Postcode</label><input class="form-control" name="postcode" value="{{ old('postcode',$customer->postcode) }}"></div><div class="span-2"><label>Address</label><textarea class="form-control" name="address" rows="2">{{ old('address',$customer->address) }}</textarea></div></div>
<div class="form-actions"><button class="btn btn-primary">Save</button><a class="btn btn-light" href="{{ route('company.customers.index') }}">Cancel</a></div></form>
@endsection
