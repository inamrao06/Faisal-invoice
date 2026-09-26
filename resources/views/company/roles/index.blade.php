@extends('layouts.app')
@section('title','Roles')
@section('page_title','Roles')
@section('content')
<style>
.page-actions h2{font-size:22px;font-weight:800;color:#0f172a}
.page-actions p{font-size:13px;color:#64748b}
.rl-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;background:#f8fafc;padding:12px 18px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.rl-table tbody td{padding:14px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.rl-table tbody tr:last-child td{border-bottom:0}
.rl-table tbody tr:hover td{background:#fafbff}
.rl-name{display:flex;align-items:center;gap:11px}
.rl-ic{width:36px;height:36px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;background:#e0e7ff;color:#4338ca;font-size:17px;flex:0 0 auto}
.rl-name strong{display:block;font-size:14px;font-weight:700;color:#0f172a}
.rl-empty{padding:56px 24px;text-align:center;color:#94a3b8}
.rl-empty i{font-size:44px;display:block;margin-bottom:14px;opacity:.5}
.rl-empty p{margin:0;font-size:14px}
</style>

<div class="page-actions">
    <div>
        <h2>Roles</h2>
        <p>Company user roles.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('company.roles.create') }}">
        <i class="ti ti-plus"></i> Add Role
    </a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table rl-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>
                            <div class="rl-name">
                                <span class="rl-ic"><i class="ti ti-shield-half"></i></span>
                                <strong>{{ $role->name }}</strong>
                            </div>
                        </td>
                        <td style="font-size:13px;color:#475569">{{ $role->description ?: '—' }}</td>
                        <td>
                            <span class="status {{ $role->is_active ? 'on' : 'off' }}">
                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('company.roles.edit',$role) }}">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="rl-empty">
                                <i class="ti ti-shield-off"></i>
                                <p>No roles yet.</p>
                                <a href="{{ route('company.roles.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus"></i> Add First Role
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($roles->hasPages())
        <div class="p-3 border-top">{{ $roles->links() }}</div>
    @endif
</div>
@endsection
