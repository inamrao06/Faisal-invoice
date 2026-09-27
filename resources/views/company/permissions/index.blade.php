@extends('layouts.app')
@section('title','Permissions')
@section('page_title','Permissions')
@section('content')
<style>
.page-actions h2{font-size:22px;font-weight:800;color:#0f172a}
.page-actions p{font-size:13px;color:#64748b}
.pm2-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;background:#f8fafc;padding:12px 18px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.pm2-table tbody td{padding:14px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.pm2-table tbody tr:last-child td{border-bottom:0}
.pm2-table tbody tr:hover td{background:#fafbff}
.pm2-role{display:flex;align-items:center;gap:11px}
.pm2-ic{width:34px;height:34px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;background:#e0e7ff;color:#4338ca;font-size:16px;flex:0 0 auto}
.pm2-role strong{display:block;font-size:14px;font-weight:700;color:#0f172a}
.pm2-mod{display:inline-flex;align-items:center;gap:6px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:700;background:#ede9fe;color:#6d28d9}
.yn{display:inline-block;min-width:44px;text-align:center;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:800;letter-spacing:.03em}
.yn.y{background:#dcfce7;color:#15803d}
.yn.n{background:#f1f5f9;color:#94a3b8}
.pm2-empty{padding:56px 24px;text-align:center;color:#94a3b8}
.pm2-empty i{font-size:44px;display:block;margin-bottom:14px;opacity:.5}
.pm2-empty p{margin:0;font-size:14px}
</style>

<div class="page-actions">
    <div>
        <h2>Permissions</h2>
        <p>Module access by role.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('company.permissions.create') }}">
        <i class="ti ti-plus"></i> Add Permission
    </a>
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table pm2-table mb-0 align-middle" data-dx-grid>
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Module</th>
                    <th class="text-center">Create</th>
                    <th class="text-center">Update</th>
                    <th class="text-center">View</th>
                    <th class="text-center">Delete</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td>
                            <div class="pm2-role">
                                <span class="pm2-ic"><i class="ti ti-shield-half"></i></span>
                                <strong>{{ $permission->role?->name ?? '—' }}</strong>
                            </div>
                        </td>
                        <td><span class="pm2-mod"><i class="ti ti-puzzle-2"></i>{{ $permission->module_name }}</span></td>
                        <td class="text-center"><span class="yn {{ $permission->can_create ? 'y' : 'n' }}">{{ $permission->can_create ? 'YES' : 'NO' }}</span></td>
                        <td class="text-center"><span class="yn {{ $permission->can_update ? 'y' : 'n' }}">{{ $permission->can_update ? 'YES' : 'NO' }}</span></td>
                        <td class="text-center"><span class="yn {{ $permission->can_view ? 'y' : 'n' }}">{{ $permission->can_view ? 'YES' : 'NO' }}</span></td>
                        <td class="text-center"><span class="yn {{ $permission->can_delete ? 'y' : 'n' }}">{{ $permission->can_delete ? 'YES' : 'NO' }}</span></td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('company.permissions.edit',$permission) }}">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="pm2-empty">
                                <i class="ti ti-lock"></i>
                                <p>No permissions defined yet.</p>
                                <a href="{{ route('company.permissions.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-plus"></i> Add First Permission
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($permissions->hasPages())
        <div class="p-3 border-top">{{ $permissions->links() }}</div>
    @endif
</div>
@endsection
