@extends('layouts.app')
@section('title','Users')
@section('page_title','Users')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <form class="app-search mb-0" method="GET">
                        <input data-panel-search class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </form>
                    @if(request('q'))
                        <a href="{{ route('users.index') }}" class="btn btn-soft-secondary btn-sm">Clear</a>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-1">
                    <a href="{{ route('users.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-user-plus fs-sm me-2"></i>Create User
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>User</th>
                            <th>User Type</th>
                            <th>Company</th>
                            <th>Access Role</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded {{ $user->isSuperUser() ? 'bg-primary text-white' : 'bg-primary bg-opacity-10 text-primary' }} fw-semibold">
                                                {{ strtoupper(substr($user->name,0,1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $user->name }}</h6>
                                            <p class="cell-sub">
                                                {{ $user->email }}
                                                @if($user->phone) &middot; {{ $user->phone }} @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($user->isSuperUser())
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold"><i class="ti ti-shield-star me-1"></i>Super Admin</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-body fw-semibold"><i class="ti ti-building-community me-1"></i>Company Admin</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->company)
                                        <h6 class="cell-title">{{ $user->company->name }}</h6>
                                        <p class="cell-sub">{{ $user->company->code }}</p>
                                    @else
                                        <span class="text-muted fst-italic">All companies</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isSuperUser() || (int)($user->company_role_num ?? 0) === 0)
                                        <span class="badge bg-info bg-opacity-10 text-info fw-semibold">
                                            <i class="ti {{ $user->isSuperUser() ? 'ti-infinity' : 'ti-shield-check' }} me-1"></i>{{ $user->accessLabel() }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold">
                                            <i class="ti ti-shield-half me-1"></i>{{ $user->accessLabel() }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if(auth()->user()->isSuperUser() && !session()->has('impersonation') && $user->is_active && !$user->is(auth()->user()))
                                        <form method="POST" action="{{ route('users.impersonate',$user) }}" class="d-inline" data-confirm-impersonate data-user-name="{{ $user->name }}">
                                            @csrf
                                            <button class="btn btn-soft-secondary btn-sm" type="submit" title="Log in as {{ $user->name }}">
                                                <i class="ti ti-user-share"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('users.edit',$user) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    @if($user->is_active && !$user->is(auth()->user()))
                                        <form method="POST" action="{{ route('users.destroy',$user) }}" class="d-inline" data-confirm-disable data-user-name="{{ $user->name }}">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-soft-danger btn-sm" type="submit" title="Disable user" aria-label="Disable {{ $user->name }}">
                                                <i class="ti ti-user-x"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="table-empty">
                                        <i class="ti ti-users"></i>
                                        <p>No users found{{ request('q') ? ' for "'.request('q').'"' : '' }}.</p>
                                        <a href="{{ route('users.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-user-plus me-1"></i>Create First User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12">{{ $users->total() }} user{{ $users->total() !== 1 ? 's' : '' }} found</span>
                @if($users->hasPages()){{ $users->links() }}@endif
            </div>
        </div>
    </div>
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
