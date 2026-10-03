@extends('layouts.app')
@section('title','Employees')
@section('page_title','Employees')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-light justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap" data-panel-tools>
                    <form class="app-search mb-0" method="GET">
                        <input data-panel-search class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search employees...">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </form>
                    @if(request('q') || request('status'))
                        <a href="{{ route('company.employees.index') }}" class="btn btn-soft-secondary btn-sm">Clear</a>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold me-1">{{ $activeCount }} active</span>
                    <a href="{{ route('company.employees.create') }}" class="btn btn-primary ms-1">
                        <i class="ti ti-user-plus fs-sm me-2"></i>Add Employee
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-centered table-hover w-100 mb-0">
                    <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                        <tr class="text-uppercase fs-xxs">
                            <th>Employee</th>
                            <th>Designation</th>
                            <th>Contact</th>
                            <th class="text-end">Monthly Salary</th>
                            <th>Status</th>
                            <th class="text-center" style="width:1%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-md me-2">
                                            <span class="avatar-title rounded bg-primary bg-opacity-10 text-primary fw-semibold">
                                                {{ strtoupper(substr($employee->name,0,1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="cell-title">{{ $employee->name }}</h6>
                                            <p class="cell-sub">{{ $employee->employee_code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="cell-title">{{ $employee->designation ?: '—' }}</h6>
                                    <p class="cell-sub">{{ $employee->department ?: 'No department' }}</p>
                                </td>
                                <td>
                                    <p class="cell-sub mb-0">
                                        @if($employee->phone)<i class="ti ti-phone me-1"></i>{{ $employee->phone }}@endif
                                    </p>
                                    @if($employee->email)
                                        <p class="cell-sub mb-0"><i class="ti ti-mail me-1"></i>{{ $employee->email }}</p>
                                    @endif
                                    @if(!$employee->phone && !$employee->email)
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <h6 class="cell-title mb-0">{{ $symbol }} {{ number_format($employee->expectedNet(),2) }}</h6>
                                    <p class="cell-sub">Basic {{ $symbol }} {{ number_format($employee->basic_salary,2) }}</p>
                                </td>
                                <td>
                                    @if($employee->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success fw-semibold"><i class="ti ti-circle-check me-1"></i>Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger fw-semibold"><i class="ti ti-circle-x me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <a class="btn btn-soft-secondary btn-sm" href="{{ route('company.employees.show',$employee) }}" title="View">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <a class="btn btn-soft-primary btn-sm" href="{{ route('company.employees.edit',$employee) }}" title="Edit">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    @if(!$employee->salaries_count)
                                        <form method="POST" action="{{ route('company.employees.destroy',$employee) }}" class="d-inline"
                                              onsubmit="return confirm('Delete {{ $employee->name }}?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-soft-danger btn-sm" type="submit" title="Delete">
                                                <i class="ti ti-trash"></i>
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
                                        <p>No employees found{{ request('q') ? ' for "'.request('q').'"' : '' }}.</p>
                                        <a href="{{ route('company.employees.create') }}" class="btn btn-primary mt-3">
                                            <i class="ti ti-user-plus me-1"></i>Add First Employee
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer py-2 d-flex align-items-center justify-content-between gap-2 flex-wrap">
                <span class="text-muted fs-12">{{ $employees->total() }} employee{{ $employees->total() !== 1 ? 's' : '' }} found</span>
                @if($employees->hasPages()){{ $employees->links() }}@endif
            </div>
        </div>
    </div>
</div>
@endsection
