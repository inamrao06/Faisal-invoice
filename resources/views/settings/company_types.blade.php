@extends('layouts.app') @section('title','Company Types') @section('page_title','Company Type') @section('content')
<form class="panel" method="POST" action="{{ route('settings.company-types.update') }}">@csrf
<div class="panel-head"><div><h3>Fixed Company Types</h3><p>Only name and active status can be changed.</p></div><button class="btn btn-primary">Save Types</button></div>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Code</th><th>Name</th><th>Status</th></tr></thead><tbody>@foreach($types as $type)<tr><td><strong>{{ $type->code }}</strong></td><td><input class="form-control" name="types[{{ $type->id }}][name]" value="{{ $type->name }}"></td><td><label class="check"><input type="checkbox" name="types[{{ $type->id }}][is_active]" value="1" @checked($type->is_active)> Active</label></td></tr>@endforeach</tbody></table></div></form>
@endsection
