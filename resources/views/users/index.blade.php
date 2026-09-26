@extends('layouts.app')
@section('title','Users')
@section('page_title','Users')
@section('content')
<style>
.ui-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.ui-header h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
.ui-header p{margin:3px 0 0;font-size:13px;color:#64748b}
.ui-search{display:flex;gap:8px;margin-bottom:0}
.ui-search .form-control{width:240px;border-radius:8px;font-size:13px}
.ui-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;background:#f8fafc;padding:12px 18px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.ui-table tbody td{padding:13px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.ui-table tbody tr:last-child td{border-bottom:0}
.ui-table tbody tr:hover td{background:#fafbff}
.u-av{width:40px;height:40px;border-radius:50%;display:grid;place-items:center;font-weight:900;font-size:16px;flex:0 0 auto}
.u-av.super{background:#fef3c7;color:#d97706}
.u-av.admin{background:#dbeafe;color:#2563eb}
.u-name strong{display:block;font-size:14px;font-weight:700;color:#0f172a}
.u-name small{font-size:12px;color:#64748b}
.type-super{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:700;background:#fef3c7;color:#92400e}
.type-admin{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:700;background:#dbeafe;color:#1d4ed8}
.role-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:800}
.role-full{background:#dcfce7;color:#15803d}
.role-restricted{background:#fef3c7;color:#92400e}
.ui-empty{padding:56px 24px;text-align:center;color:#94a3b8}
.ui-empty i{font-size:44px;display:block;margin-bottom:14px;opacity:.5}
.panel-toolbar{padding:16px 18px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
</style>

<div class="ui-header">
    <div>
        <h2>User Management</h2>
        <p>Super Admin and Company Admin accounts for the booking system.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('users.create') }}">
        <i class="bi bi-person-plus"></i> Create User
    </a>
</div>

<div class="panel">
    <div class="panel-toolbar">
        <form class="ui-search" method="GET">
            <input class="form-control" name="q" value="{{ request('q') }}"
                   placeholder="Search name or email…">
            <button class="btn btn-dark" type="submit">
                <i class="bi bi-search"></i>
            </button>
            @if(request('q'))
                <a href="{{ route('users.index') }}" class="btn btn-light">Clear</a>
            @endif
        </form>
        <small style="color:#94a3b8">
            {{ $users->total() }} user{{ $users->total() !== 1 ? 's' : '' }} found
        </small>
    </div>

    <div class="table-responsive">
        <table class="table ui-table mb-0 align-middle">
            <thead>
                <tr>
                    <th>User</th>
                    <th>User Type</th>
                    <th>Company</th>
                    <th>Access Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="u-av {{ $user->isSuperUser() ? 'super' : 'admin' }}">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                                <div class="u-name">
                                    <strong>{{ $user->name }}</strong>
                                    <small>{{ $user->email }}
                                        @if($user->phone) · {{ $user->phone }}@endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($user->isSuperUser())
                                <span class="type-super">
                                    <i class="bi bi-shield-star-fill"></i> Super Admin
                                </span>
                            @else
                                <span class="type-admin">
                                    <i class="bi bi-building-check"></i> Company Admin
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->company)
                                <div style="font-size:13px;font-weight:700;color:#0f172a">
                                    {{ $user->company->name }}
                                </div>
                                <div style="font-size:11px;color:#94a3b8">{{ $user->company->code }}</div>
                            @else
                                <span style="font-size:13px;color:#94a3b8;font-style:italic">
                                    All companies
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($user->isSuperUser())
                                <span class="role-badge role-full">
                                    <i class="bi bi-infinity"></i> Global
                                </span>
                            @elseif((int)($user->company_role_num ?? 0) === 0)
                                <span class="role-badge role-full">
                                    <i class="bi bi-shield-fill-check"></i> Full Access
                                </span>
                            @else
                                <span class="role-badge role-restricted">
                                    <i class="bi bi-shield-half"></i> Role {{ $user->company_role_num }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="status {{ $user->is_active ? 'on' : 'off' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('users.edit',$user) }}">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            @if($user->is_active && !$user->is(auth()->user()))
                                <form method="POST"
                                      action="{{ route('users.destroy',$user) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('Disable this user?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light ms-1" type="submit">
                                        <i class="bi bi-person-x"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="ui-empty">
                                <i class="bi bi-people"></i>
                                <p>No users found{{ request('q') ? ' for "'.request('q').'"' : '' }}.</p>
                                <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                                    <i class="bi bi-person-plus"></i> Create First User
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="p-3 border-top">{{ $users->links() }}</div>
    @endif
</div>
@endsection
