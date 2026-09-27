@extends('layouts.app')
@section('title','Companies')
@section('page_title','Companies')
@section('content')

<div class="ci-header">
    <div>
        <h2>Company Accounts</h2>
        <p>All registered companies in the booking service system.</p>
    </div>
    @can('manage-branches')
        <a class="btn btn-primary" href="{{ route('companies.create') }}">
            <i class="ti ti-plus"></i> Create Company
        </a>
    @endcan
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table ci-table mb-0 align-middle" data-dx-grid>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Currency</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="co-avatar">
                                    @if($company->logo_path)
                                        <img src="{{ asset('storage/'.$company->logo_path) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($company->name,0,1)) }}
                                    @endif
                                </div>
                                <div class="co-name">
                                    <strong>{{ $company->name }}</strong>
                                    <small>{{ $company->code }}
                                        @if($company->company_email) · {{ $company->company_email }}@endif
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="type-tag">
                                <i class="ti ti-tag-filled"></i>
                                {{ str($company->type)->replace('_',' ')->title() }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#475569;display:grid;gap:2px">
                                @if($company->phone)
                                    <span><i class="ti ti-phone me-1"></i>{{ $company->phone }}</span>
                                @endif
                                @if($company->city)
                                    <span><i class="ti ti-map-pin me-1"></i>{{ $company->city }}</span>
                                @endif
                                @if(!$company->phone && !$company->city)
                                    <span style="color:#cbd5e1">—</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($company->currency)
                                <strong style="font-size:13px;color:#0f172a">{{ $company->currency->code }}</strong>
                                <small style="display:block;color:#94a3b8">{{ $company->currency->symbol }}</small>
                            @else
                                <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="user-count">{{ $company->users_count ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="status {{ $company->is_active ? 'on' : 'off' }}">
                                {{ $company->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end" style="white-space:nowrap">
                            <a class="btn btn-sm btn-light" href="{{ route('companies.show',$company) }}">
                                <i class="ti ti-eye"></i> View
                            </a>
                            @can('manage-branches')
                                <a class="btn btn-sm btn-outline-primary ms-1"
                                   href="{{ route('companies.edit',$company) }}">
                                    <i class="ti ti-pencil"></i> Edit
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="ci-empty">
                                <i class="ti ti-buildings"></i>
                                <p>No companies registered yet.</p>
                                @can('manage-branches')
                                    <a href="{{ route('companies.create') }}" class="btn btn-primary mt-3">
                                        <i class="ti ti-plus"></i> Create First Company
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
        <div class="p-3 border-top">{{ $companies->links() }}</div>
    @endif
</div>
@endsection
