@extends('layouts.app')
@section('title','Users')
@section('page_title','Users')
@section('content')

<div class="ui-header">
    <div>
        <h2>User Management</h2>
        <p>Super Admin and Company Admin accounts for the booking system.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('users.create') }}">
        <i class="ti ti-user-plus"></i> Create User
    </a>
</div>

<div class="panel">
    <div class="panel-toolbar">
        <form class="ui-search" method="GET">
            <input class="form-control" name="q" value="{{ request('q') }}"
                   placeholder="Search name or email…">
            <button class="btn btn-dark" type="submit">
                <i class="ti ti-search"></i>
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
        <table class="table ui-table mb-0 align-middle" data-dx-grid>
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
                                    <i class="ti ti-shield-star"></i> Super Admin
                                </span>
                            @else
                                <span class="type-admin">
                                    <i class="ti ti-building-community"></i> Company Admin
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
                                    <i class="ti ti-infinity"></i> Global
                                </span>
                            @elseif((int)($user->company_role_num ?? 0) === 0)
                                <span class="role-badge role-full">
                                    <i class="ti ti-shield-check-filled"></i> Full Access
                                </span>
                            @else
                                <span class="role-badge role-restricted">
                                    <i class="ti ti-shield-half"></i> Role {{ $user->company_role_num }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="status {{ $user->is_active ? 'on' : 'off' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            @if(auth()->user()->isSuperUser() && !session()->has('impersonation') && $user->is_active && !$user->is(auth()->user()))
                                <form method="POST" action="{{ route('users.impersonate', $user) }}" class="d-inline" data-confirm-impersonate data-user-name="{{ $user->name }}">
                                    @csrf
                                    <button class="btn btn-sm btn-light me-1" type="submit" title="Log in as {{ $user->name }}"><i class="ti ti-user-share me-1"></i>Log in as</button>
                                </form>
                            @endif
                            <a class="btn btn-sm btn-outline-primary"
                               href="{{ route('users.edit',$user) }}">
                                <i class="ti ti-pencil"></i> Edit
                            </a>
                            @if($user->is_active && !$user->is(auth()->user()))
                                <form method="POST"
                                      action="{{ route('users.destroy',$user) }}"
                                      style="display:inline"
                                      data-confirm-disable data-user-name="{{ $user->name }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light ms-1" type="submit" title="Disable user" aria-label="Disable {{ $user->name }}">
                                        <i class="ti ti-user-x"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="ui-empty">
                                <i class="ti ti-users"></i>
                                <p>No users found{{ request('q') ? ' for "'.request('q').'"' : '' }}.</p>
                                <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                                    <i class="ti ti-user-plus"></i> Create First User
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

@push('styles')
<link href="{{ asset('paces/assets/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('paces/assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
document.querySelectorAll('[data-confirm-disable]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const name = form.dataset.userName || 'this user';
        if (!window.Swal) {
            if (window.confirm('Disable ' + name + '?')) form.submit();
            return;
        }
        const result = await Swal.fire({
            title: 'Disable user?',
            text: name + ' will no longer be able to sign in.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Disable user',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            reverseButtons: true,
            focusCancel: true,
            background: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#252831' : '#ffffff',
            color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#f5f7fa' : '#26313d'
        });
        if (result.isConfirmed) form.submit();
    });
});
document.querySelectorAll('[data-confirm-impersonate]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const name = form.dataset.userName || 'this user';
        if (!window.Swal) {
            if (window.confirm('Log in as ' + name + '?')) form.submit();
            return;
        }
        const result = await Swal.fire({
            title: 'Log in as ' + name + '?',
            text: 'You can return to your Super Admin account from the banner at the top.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Log in as user',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#236dc9',
            reverseButtons: true
        });
        if (result.isConfirmed) form.submit();
    });
});
@if(session('success'))
if (window.Swal) {
    document.querySelector('.content-page .alert.alert-success')?.remove();
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: @json(session('success')),
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true
    });
}
@endif
</script>
@endpush
