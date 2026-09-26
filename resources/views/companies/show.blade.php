@extends('layouts.app')
@section('title', $company->name)
@section('page_title', 'Company Profile')
@section('content')
<style>
.cp-hero{display:flex;align-items:center;gap:24px;background:linear-gradient(135deg,#0c1a36 0%,#1a3560 50%,#2563eb 100%);color:#fff;border-radius:14px;padding:28px 32px;margin-bottom:22px;box-shadow:0 12px 40px rgba(37,99,235,.2)}
.cp-logo{width:90px;height:90px;border-radius:14px;background:#fff;display:grid;place-items:center;font-size:34px;font-weight:900;color:#2563eb;overflow:hidden;flex:0 0 auto;box-shadow:0 8px 28px rgba(0,0,0,.22)}
.cp-logo img{width:100%;height:100%;object-fit:contain;padding:8px}
.cp-body{flex:1;min-width:0}
.cp-type-label{display:inline-block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#93c5fd;margin-bottom:6px}
.cp-body h2{font-size:28px;font-weight:800;margin:0 0 8px}
.cp-meta{display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:#bfdbfe}
.cp-meta span{display:flex;align-items:center;gap:5px}
.cp-meta a{color:#93c5fd}
.cp-actions{display:flex;gap:10px;flex-wrap:wrap;align-self:flex-start}
.ic{background:#fff;border:1px solid #e2e8f0;border-radius:12px;box-shadow:0 2px 12px rgba(15,23,42,.05);margin-bottom:16px;overflow:hidden}
.ic-head{display:flex;align-items:center;gap:10px;padding:15px 20px;background:#fafbfc;border-bottom:1px solid #f1f5f9}
.ic-head i.icon{width:30px;height:30px;border-radius:8px;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-size:13px;flex:0 0 auto}
.ic-head h4{margin:0;font-size:13px;font-weight:800;color:#0f172a}
.ic-head .ic-head-action{margin-left:auto}
.ic-row{display:grid;grid-template-columns:repeat(3,minmax(0,1fr))}
.ic-row.r2{grid-template-columns:repeat(2,minmax(0,1fr))}
.ic-cell{padding:13px 20px;border-right:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9}
.ic-row .ic-cell:last-child,.ic-row.r3 .ic-cell:nth-child(3n){border-right:0}
.ic-row:last-of-type .ic-cell{border-bottom:0}
.ic-cell small{display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:3px}
.ic-cell strong{font-size:14px;color:#1e293b;word-break:break-word}
.ic-cell a{color:#2563eb;font-size:13px}
.doc-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:18px}
.doc-card{border:1px solid #e2e8f0;border-radius:10px;overflow:hidden}
.doc-card-lbl{padding:8px 12px;background:#f8fafc;border-bottom:1px solid #f1f5f9;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#64748b;display:flex;align-items:center;gap:6px}
.doc-card-body{height:104px;display:grid;place-items:center;background:#fff}
.doc-card-body img{max-width:100%;max-height:100%;object-fit:contain;padding:8px}
.no-doc{color:#cbd5e1;text-align:center;font-size:12px}.no-doc i{font-size:26px;display:block;margin-bottom:4px}
.u-row{display:flex;align-items:center;gap:12px;padding:12px 20px;border-bottom:1px solid #f1f5f9}
.u-row:last-child{border-bottom:0}
.u-av{width:38px;height:38px;border-radius:50%;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-weight:900;font-size:15px;flex:0 0 auto}
.u-name{font-weight:700;font-size:13px;color:#1e293b;line-height:1.3}
.u-email{font-size:12px;color:#64748b}
.badge-full{background:#dcfce7;color:#15803d;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:800;white-space:nowrap}
.badge-role{background:#fef3c7;color:#92400e;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:800;white-space:nowrap}
.stat-item{display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:8px}
.stat-item span{font-size:13px;color:#64748b}
.stat-item strong{font-size:18px;font-weight:800;color:#0f172a}
@media(max-width:900px){.cp-hero{flex-direction:column;align-items:flex-start}.ic-row{grid-template-columns:1fr 1fr}.ic-row.r2{grid-template-columns:1fr}.doc-grid{grid-template-columns:1fr 1fr}}
@media(max-width:560px){.ic-row,.ic-row.r2{grid-template-columns:1fr}.ic-cell{border-right:0}.doc-grid{grid-template-columns:1fr}}
</style>

{{-- HERO --}}
<div class="cp-hero">
    <div class="cp-logo">
        @if($company->logo_path)
            <img src="{{ asset('storage/'.$company->logo_path) }}" alt="{{ $company->name }}">
        @else
            {{ strtoupper(substr($company->name,0,1)) }}
        @endif
    </div>
    <div class="cp-body">
        <div class="cp-type-label">{{ str($company->type)->replace('_',' ')->title() }}</div>
        <h2>{{ $company->name }}</h2>
        <div class="cp-meta">
            @if($company->company_email)
                <span><i class="bi bi-envelope-fill"></i>{{ $company->company_email }}</span>
            @endif
            @if($company->phone)
                <span><i class="bi bi-telephone-fill"></i>{{ $company->phone }}</span>
            @endif
            @if($company->city)
                <span><i class="bi bi-geo-alt-fill"></i>{{ $company->city }}</span>
            @endif
            @if($company->website)
                <span><i class="bi bi-globe"></i>
                    <a href="{{ $company->website }}" target="_blank">
                        {{ str($company->website)->after('//')->before('/') }}
                    </a>
                </span>
            @endif
            <span>
                <i class="bi bi-circle-fill"
                   style="font-size:7px;color:{{ $company->is_active ? '#4ade80' : '#94a3b8' }}"></i>
                {{ $company->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
    <div class="cp-actions">
        @can('manage-branches')
            <a class="btn btn-light" href="{{ route('companies.edit',$company) }}">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
        @endcan
        <a class="btn btn-outline-light" href="{{ route('companies.index') }}">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row g-3">

{{-- LEFT COLUMN --}}
<div class="col-xl-8">

    {{-- Identity --}}
    <div class="ic">
        <div class="ic-head"><i class="bi bi-building icon"></i><h4>Company Identity</h4></div>
        <div class="ic-row">
            <div class="ic-cell"><small>Code</small><strong>{{ $company->code }}</strong></div>
            <div class="ic-cell"><small>Type</small><strong>{{ str($company->type)->replace('_',' ')->title() }}</strong></div>
            <div class="ic-cell"><small>Currency</small><strong>{{ $company->currency?->symbol }} {{ $company->currency?->code ?? '—' }}</strong></div>
        </div>
        <div class="ic-row">
            <div class="ic-cell"><small>Tax / Reg. No.</small><strong>{{ $company->tax_number ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Invoice Prefix</small><strong>{{ $company->invoice_prefix ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Status</small>
                <strong><span class="status {{ $company->is_active ? 'on' : 'off' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></strong>
            </div>
        </div>
    </div>

    {{-- Contact --}}
    <div class="ic">
        <div class="ic-head"><i class="bi bi-envelope icon"></i><h4>Contact Information</h4></div>
        <div class="ic-row">
            <div class="ic-cell"><small>Company Email</small><strong>{{ $company->company_email ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Phone</small><strong>{{ $company->phone ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Website</small>
                <strong>
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank">
                            {{ str($company->website)->after('//')->limit(28) }}
                        </a>
                    @else —
                    @endif
                </strong>
            </div>
        </div>
        <div class="ic-row r2">
            <div class="ic-cell"><small>City</small><strong>{{ $company->city ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Address</small><strong>{{ $company->address ?? '—' }}</strong></div>
        </div>
    </div>

    {{-- Authorized Person --}}
    <div class="ic">
        <div class="ic-head"><i class="bi bi-person-vcard icon"></i><h4>Authorized Person</h4></div>
        <div class="ic-row">
            <div class="ic-cell"><small>Authorized Person</small><strong>{{ $company->authorized_person ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Designation</small><strong>{{ $company->designation ?? '—' }}</strong></div>
            <div class="ic-cell"><small>Manager / Contact</small><strong>{{ $company->manager_name ?? '—' }}</strong></div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="ic">
        <div class="ic-head"><i class="bi bi-images icon"></i><h4>Logo, Signature &amp; Stamp</h4></div>
        <div class="doc-grid">
            <div class="doc-card">
                <div class="doc-card-lbl"><i class="bi bi-building"></i> Company Logo</div>
                <div class="doc-card-body">
                    @if($company->logo_path)
                        <img src="{{ asset('storage/'.$company->logo_path) }}" alt="Logo">
                    @else
                        <div class="no-doc"><i class="bi bi-image"></i>No logo</div>
                    @endif
                </div>
            </div>
            <div class="doc-card">
                <div class="doc-card-lbl"><i class="bi bi-pen"></i> Signature</div>
                <div class="doc-card-body">
                    @if($company->signature_path)
                        <img src="{{ asset('storage/'.$company->signature_path) }}" alt="Signature">
                    @else
                        <div class="no-doc"><i class="bi bi-pen"></i>No signature</div>
                    @endif
                </div>
            </div>
            <div class="doc-card">
                <div class="doc-card-lbl"><i class="bi bi-award"></i> Stamp / Seal</div>
                <div class="doc-card-body">
                    @if($company->stamp_path)
                        <img src="{{ asset('storage/'.$company->stamp_path) }}" alt="Stamp">
                    @else
                        <div class="no-doc"><i class="bi bi-award"></i>No stamp</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- RIGHT COLUMN --}}
<div class="col-xl-4">

    {{-- Quick Stats --}}
    <div class="ic mb-3">
        <div class="ic-head"><i class="bi bi-bar-chart-line icon"></i><h4>Quick Stats</h4></div>
        <div style="padding:14px 18px;display:grid;gap:8px">
            <div class="stat-item">
                <span><i class="bi bi-people me-2 text-primary"></i>Total Users</span>
                <strong>{{ $company->users->count() }}</strong>
            </div>
            <div class="stat-item">
                <span><i class="bi bi-shield-fill-check me-2 text-success"></i>Full Access</span>
                <strong style="color:#15803d">{{ $company->users->where('company_role_num',0)->count() }}</strong>
            </div>
            <div class="stat-item">
                <span><i class="bi bi-person-check me-2 text-primary"></i>Active Users</span>
                <strong style="color:#2563eb">{{ $company->users->where('is_active',true)->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- Company Admins --}}
    <div class="ic">
        <div class="ic-head">
            <i class="bi bi-people icon"></i>
            <h4>Company Admins</h4>
            @can('manage-branches')
                <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary ic-head-action">
                    <i class="bi bi-plus-lg"></i> Add
                </a>
            @endcan
        </div>
        @forelse($company->users as $user)
            <div class="u-row">
                <div class="u-av">{{ strtoupper(substr($user->name,0,1)) }}</div>
                <div style="flex:1;min-width:0">
                    <div class="u-name">{{ $user->name }}</div>
                    <div class="u-email">{{ $user->email }}</div>
                </div>
                @if((int)($user->company_role_num ?? 0) === 0)
                    <span class="badge-full"><i class="bi bi-shield-fill-check"></i> Full</span>
                @else
                    <span class="badge-role">Role {{ $user->company_role_num }}</span>
                @endif
            </div>
        @empty
            <div style="padding:28px 20px;text-align:center;color:#94a3b8">
                <i class="bi bi-people" style="font-size:30px;display:block;margin-bottom:8px"></i>
                No users assigned yet.
            </div>
        @endforelse
    </div>

</div>
</div>
@endsection
