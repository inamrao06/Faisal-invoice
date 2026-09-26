@extends('layouts.app')
@section('title','Companies')
@section('page_title','Companies')
@section('content')
<style>
.ci-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px}
.ci-header h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
.ci-header p{margin:3px 0 0;font-size:13px;color:#64748b}
.ci-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;background:#f8fafc;padding:12px 18px;border-bottom:2px solid #e7ecf3;white-space:nowrap}
.ci-table tbody td{padding:14px 18px;vertical-align:middle;border-bottom:1px solid #f1f5f9}
.ci-table tbody tr:last-child td{border-bottom:0}
.ci-table tbody tr:hover td{background:#fafbff}
.co-avatar{width:42px;height:42px;border-radius:10px;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-weight:900;font-size:17px;overflow:hidden;flex:0 0 auto}
.co-avatar img{width:100%;height:100%;object-fit:contain;padding:4px}
.co-name strong{display:block;font-size:14px;font-weight:700;color:#0f172a}
.co-name small{font-size:12px;color:#64748b}
.type-tag{display:inline-flex;align-items:center;gap:5px;padding:4px 11px;border-radius:999px;font-size:12px;font-weight:700;background:#ede9fe;color:#6d28d9}
.ci-empty{padding:56px 24px;text-align:center;color:#94a3b8}
.ci-empty i{font-size:44px;display:block;margin-bottom:14px;opacity:.5}
.ci-empty p{margin:0;font-size:14px}
.user-count{display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:#dbeafe;color:#2563eb;font-weight:800;font-size:14px}
</style>

<div class="ci-header">
    <div>
        <h2>Company Accounts</h2>
        <p>All registered companies in the booking service system.</p>
    </div>
    @can('manage-branches')
        <a class="btn btn-primary" href="{{ route('companies.create') }}">
            <i class="bi bi-plus-lg"></i> Create Company
        </a>
    @endcan
</div>

<div class="panel">
    <div class="table-responsive">
        <table class="table ci-table mb-0 align-middle">
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
                                <i class="bi bi-tag-fill"></i>
                                {{ str($company->type)->replace('_',' ')->title() }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#475569;display:grid;gap:2px">
                                @if($company->phone)
                                    <span><i class="bi bi-telephone me-1"></i>{{ $company->phone }}</span>
                                @endif
                                @if($company->city)
                                    <span><i class="bi bi-geo-alt me-1"></i>{{ $company->city }}</span>
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
                                <i class="bi bi-eye"></i> View
                            </a>
                            @can('manage-branches')
                                <a class="btn btn-sm btn-outline-primary ms-1"
                                   href="{{ route('companies.edit',$company) }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="ci-empty">
                                <i class="bi bi-buildings"></i>
                                <p>No companies registered yet.</p>
                                @can('manage-branches')
                                    <a href="{{ route('companies.create') }}" class="btn btn-primary mt-3">
                                        <i class="bi bi-plus-lg"></i> Create First Company
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
