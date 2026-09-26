@extends('layouts.app')
@section('title','Super Admin Dashboard')
@section('page_title','Dashboard')
@section('content')
<style>
/* ── Hero ──────────────────────────────────────────────────── */
.sa-hero{position:relative;overflow:hidden;background:linear-gradient(135deg,#0c1a36 0%,#1e3a6e 45%,#2563eb 100%);border-radius:16px;padding:32px 36px;margin-bottom:24px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;box-shadow:0 16px 48px rgba(37,99,235,.25)}
.sa-hero::before{content:'';position:absolute;right:-60px;top:-60px;width:320px;height:320px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none}
.sa-hero::after{content:'';position:absolute;right:80px;bottom:-80px;width:200px;height:200px;border-radius:50%;background:rgba(20,184,166,.12);pointer-events:none}
.sa-hero-text span{font-size:11px;text-transform:uppercase;letter-spacing:.14em;color:#93c5fd;font-weight:700;display:block;margin-bottom:8px}
.sa-hero-text h2{font-size:30px;font-weight:900;margin:0 0 10px;line-height:1.2}
.sa-hero-text p{font-size:14px;color:#bfdbfe;margin:0;max-width:560px;line-height:1.6}
.sa-hero-actions{display:flex;gap:10px;flex-wrap:wrap;flex:0 0 auto}

/* ── Metric cards ─────────────────────────────────────────── */
.sa-metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:24px}
.sa-metric{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 4px 16px rgba(15,23,42,.05);transition:.2s}
.sa-metric:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(15,23,42,.1)}
.sa-metric-icon{width:52px;height:52px;border-radius:12px;display:grid;place-items:center;font-size:22px;flex:0 0 auto}
.sa-metric-icon.blue{background:#dbeafe;color:#2563eb}
.sa-metric-icon.green{background:#dcfce7;color:#16a34a}
.sa-metric-icon.amber{background:#fef3c7;color:#d97706}
.sa-metric-icon.rose{background:#fee2e2;color:#dc2626}
.sa-metric-body small{display:block;font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px}
.sa-metric-body strong{font-size:26px;font-weight:900;color:#0f172a;line-height:1}

/* ── Quick Links ──────────────────────────────────────────── */
.sa-quicklinks{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:24px}
.sa-ql{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:18px 16px;display:flex;flex-direction:column;gap:6px;color:#0f172a!important;box-shadow:0 2px 12px rgba(15,23,42,.04);transition:.2s;text-decoration:none!important;position:relative;overflow:hidden}
.sa-ql::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--ql-color,#2563eb);border-radius:2px 2px 0 0}
.sa-ql:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(15,23,42,.1)}
.sa-ql i.ql-icon{font-size:26px;color:var(--ql-color,#2563eb)}
.sa-ql strong{font-size:14px;font-weight:800;color:#0f172a}
.sa-ql span{font-size:12px;color:#64748b}

/* ── Panels ───────────────────────────────────────────────── */
.sa-panel{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 4px 16px rgba(15,23,42,.05);overflow:hidden}
.sa-panel-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid #f1f5f9}
.sa-panel-head h3{margin:0;font-size:15px;font-weight:800;color:#0f172a}
.sa-panel-head p{margin:2px 0 0;font-size:12px;color:#94a3b8}
.sa-panel-head a{font-size:13px;font-weight:700;color:#2563eb}
.sa-table thead th{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;background:#fafbfc;padding:10px 18px;border-bottom:1px solid #f1f5f9}
.sa-table tbody td{padding:13px 18px;font-size:13px;border-bottom:1px solid #f1f5f9;color:#1e293b}
.sa-table tbody tr:last-child td{border-bottom:0}
.sa-table tbody tr:hover td{background:#fafbff}
.co-av{width:36px;height:36px;border-radius:9px;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-weight:900;font-size:14px;overflow:hidden;flex:0 0 auto}
.co-av img{width:100%;height:100%;object-fit:contain;padding:3px}
.type-chip{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:999px;font-size:11px;font-weight:700;background:#ede9fe;color:#6d28d9}

/* ── Side panels ──────────────────────────────────────────── */
.type-row{display:flex;align-items:center;justify-content:space-between;padding:13px 20px;border-bottom:1px solid #f1f5f9}
.type-row:last-child{border-bottom:0}
.type-row span{font-size:13px;color:#374151;font-weight:600}
.scope-item{display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid #f1f5f9}
.scope-item:last-child{border-bottom:0}
.scope-icon{width:36px;height:36px;border-radius:9px;background:#dbeafe;color:#2563eb;display:grid;place-items:center;font-size:16px;flex:0 0 auto}
.scope-icon.amber{background:#fef3c7;color:#d97706}
.scope-body strong{display:block;font-size:13px;font-weight:700;color:#0f172a}
.scope-body span{font-size:12px;color:#64748b}

@media(max-width:1100px){.sa-metrics{grid-template-columns:1fr 1fr}.sa-quicklinks{grid-template-columns:1fr 1fr}}
@media(max-width:700px){.sa-hero{flex-direction:column}.sa-metrics,.sa-quicklinks{grid-template-columns:1fr}}
</style>

{{-- HERO --}}
<div class="sa-hero">
    <div class="sa-hero-text">
        <span>Booking Service System — Super Admin</span>
        <h2>Welcome back, {{ auth()->user()->name }} 👋</h2>
        <p>You have full control over all companies, admins, currency, and system settings.</p>
    </div>
    <div class="sa-hero-actions">
        <a class="btn btn-light fw-bold" href="{{ route('companies.create') }}">
            <i class="bi bi-plus-lg"></i> New Company
        </a>
        <a class="btn btn-outline-light" href="{{ route('users.create') }}">
            <i class="bi bi-person-plus"></i> New Admin
        </a>
    </div>
</div>

{{-- METRICS --}}
<div class="sa-metrics">
    @foreach($cards as $card)
        <div class="sa-metric">
            <div class="sa-metric-icon {{ $card['tone'] === 'primary' ? 'blue' : ($card['tone'] === 'success' ? 'green' : ($card['tone'] === 'warning' ? 'amber' : 'rose')) }}">
                <i class="bi bi-{{ $card['icon'] }}"></i>
            </div>
            <div class="sa-metric-body">
                <small>{{ $card['label'] }}</small>
                <strong>{{ !empty($card['money']) ? number_format($card['value'],2) : number_format($card['value']) }}</strong>
            </div>
        </div>
    @endforeach
</div>

{{-- QUICK LINKS --}}
<div class="sa-quicklinks">
    <a class="sa-ql" href="{{ route('companies.index') }}" style="--ql-color:#2563eb">
        <i class="bi bi-buildings ql-icon"></i>
        <strong>Companies</strong>
        <span>View &amp; manage all companies</span>
    </a>
    <a class="sa-ql" href="{{ route('users.index') }}" style="--ql-color:#7c3aed">
        <i class="bi bi-people ql-icon"></i>
        <strong>Users</strong>
        <span>Create admins, manage access</span>
    </a>
    <a class="sa-ql" href="{{ route('settings.company-types.index') }}" style="--ql-color:#0d9488">
        <i class="bi bi-tags ql-icon"></i>
        <strong>Company Types</strong>
        <span>Service Booking, POS, Sales</span>
    </a>
    <a class="sa-ql" href="{{ route('settings.currencies.index') }}" style="--ql-color:#d97706">
        <i class="bi bi-currency-exchange ql-icon"></i>
        <strong>Currencies</strong>
        <span>Manage {{ $currencies }} active currencies</span>
    </a>
</div>

<div class="row g-3">

    {{-- Recent Companies --}}
    <div class="col-xl-8">
        <div class="sa-panel">
            <div class="sa-panel-head">
                <div>
                    <h3>Recent Companies</h3>
                    <p>Latest registered company accounts</p>
                </div>
                <a href="{{ route('companies.index') }}">View all <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table sa-table mb-0">
                    <thead>
                        <tr>
                            <th>Company</th>
                            <th>Type</th>
                            <th>Authorized Person</th>
                            <th>Currency</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentCompanies as $company)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:10px">
                                        <div class="co-av">
                                            @if($company->logo_path)
                                                <img src="{{ asset('storage/'.$company->logo_path) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($company->name,0,1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight:700">{{ $company->name }}</div>
                                            <div style="font-size:11px;color:#94a3b8">{{ $company->company_email ?: $company->code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="type-chip"><i class="bi bi-tag-fill"></i>{{ str($company->type)->replace('_',' ')->title() }}</span></td>
                                <td>
                                    @if($company->authorized_person)
                                        <div style="font-weight:600">{{ $company->authorized_person }}</div>
                                        <div style="font-size:11px;color:#94a3b8">{{ $company->designation }}</div>
                                    @else
                                        <span style="color:#cbd5e1">—</span>
                                    @endif
                                </td>
                                <td><span style="font-weight:700">{{ $company->currency?->code ?? '—' }}</span></td>
                                <td><span class="status {{ $company->is_active ? 'on' : 'off' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <a class="btn btn-sm btn-light" href="{{ route('companies.show',$company) }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" style="padding:36px;text-align:center;color:#94a3b8">
                                No companies yet. <a href="{{ route('companies.create') }}">Create one →</a>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Side --}}
    <div class="col-xl-4 d-flex flex-column gap-3">

        {{-- Company Types --}}
        <div class="sa-panel">
            <div class="sa-panel-head">
                <div>
                    <h3>Company Types</h3>
                    <p>Available booking modules</p>
                </div>
                <a href="{{ route('settings.company-types.index') }}">Manage</a>
            </div>
            @foreach($companyTypes as $type)
                <div class="type-row">
                    <div style="display:flex;align-items:center;gap:8px">
                        <i class="bi bi-tag-fill" style="color:#7c3aed;font-size:12px"></i>
                        <span>{{ $type->name }}</span>
                    </div>
                    <span class="status {{ $type->is_active ? 'on' : 'off' }}">
                        {{ $type->is_active ? 'Active' : 'Off' }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Access Scope --}}
        <div class="sa-panel">
            <div class="sa-panel-head">
                <div><h3>Access Scope</h3><p>User privilege rules</p></div>
            </div>
            <div class="scope-item">
                <div class="scope-icon amber"><i class="bi bi-shield-star-fill"></i></div>
                <div class="scope-body">
                    <strong>Super Admin</strong>
                    <span>All companies · All settings · Create admins</span>
                </div>
            </div>
            <div class="scope-item">
                <div class="scope-icon"><i class="bi bi-building-check"></i></div>
                <div class="scope-body">
                    <strong>Admin (Role 0)</strong>
                    <span>Full access to assigned company</span>
                </div>
            </div>
            <div class="scope-item">
                <div class="scope-icon" style="background:#fef3c7;color:#92400e"><i class="bi bi-shield-half"></i></div>
                <div class="scope-body">
                    <strong>Admin (Role &gt; 0)</strong>
                    <span>Restricted by Roles &amp; Permissions</span>
                </div>
            </div>
        </div>

        {{-- Settings Shortcuts --}}
        <div class="sa-panel">
            <div class="sa-panel-head">
                <div><h3>Settings</h3><p>System configuration</p></div>
            </div>
            <div style="padding:14px;display:grid;gap:8px">
                <a href="{{ route('settings.general') }}"
                   style="display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:9px;background:#f8fafc;border:1px solid #e2e8f0;color:#1e293b!important;font-weight:600;font-size:13px;text-decoration:none">
                    <i class="bi bi-sliders" style="color:#2563eb;width:18px;text-align:center"></i> General Settings
                </a>
                <a href="{{ route('settings.theme') }}"
                   style="display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:9px;background:#f8fafc;border:1px solid #e2e8f0;color:#1e293b!important;font-weight:600;font-size:13px;text-decoration:none">
                    <i class="bi bi-palette" style="color:#7c3aed;width:18px;text-align:center"></i> Theme Settings
                </a>
                <a href="{{ route('settings.currencies.index') }}"
                   style="display:flex;align-items:center;gap:10px;padding:11px 14px;border-radius:9px;background:#f8fafc;border:1px solid #e2e8f0;color:#1e293b!important;font-weight:600;font-size:13px;text-decoration:none">
                    <i class="bi bi-currency-exchange" style="color:#0d9488;width:18px;text-align:center"></i> Currency Settings
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
