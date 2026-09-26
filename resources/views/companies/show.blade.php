@extends('layouts.app')
@section('title', $company->name)
@section('page_title', 'Company Profile')
@section('content')
<div class="company-detail-header">
    <div class="company-detail-intro">
        <span class="company-detail-logo">
            @if($company->logo_path)
                <img src="{{ asset('storage/'.$company->logo_path) }}" alt="{{ $company->name }} logo">
            @else
                {{ strtoupper(substr($company->name, 0, 1)) }}
            @endif
        </span>
        <div class="min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <h2 class="company-detail-name">{{ $company->name }}</h2>
                <span class="badge {{ $company->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="company-detail-meta">
                <span>{{ $company->code }}</span>
                <span>{{ str($company->type)->replace('_', ' ')->title() }}</span>
                @if($company->city)<span><i class="ti ti-map-pin"></i> {{ $company->city }}</span>@endif
            </div>
        </div>
    </div>
    <div class="company-detail-actions">
        <a href="{{ route('companies.index') }}" class="btn btn-light"><i class="ti ti-arrow-left me-1"></i>Back</a>
        @can('manage-branches')
            <a href="{{ route('companies.edit', $company) }}" class="btn btn-primary"><i class="ti ti-edit me-1"></i>Edit Company</a>
        @endcan
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <section class="card company-detail-card">
            <div class="card-header"><h3 class="card-title mb-0"><i class="ti ti-building me-2 text-primary"></i>Company Identity</h3></div>
            <div class="card-body company-detail-grid">
                <div><span>Company Code</span><strong>{{ $company->code }}</strong></div>
                <div><span>Company Type</span><strong>{{ str($company->type)->replace('_', ' ')->title() }}</strong></div>
                <div><span>Currency</span><strong>{{ $company->currency?->symbol }} {{ $company->currency?->code ?? '-' }}</strong></div>
                <div><span>Tax / Registration Number</span><strong>{{ $company->tax_number ?: '-' }}</strong></div>
                <div><span>Invoice Prefix</span><strong>{{ $company->invoice_prefix ?: '-' }}</strong></div>
                <div><span>Status</span><strong><span class="badge {{ $company->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $company->is_active ? 'Active' : 'Inactive' }}</span></strong></div>
            </div>
        </section>

        <section class="card company-detail-card">
            <div class="card-header"><h3 class="card-title mb-0"><i class="ti ti-address-book me-2 text-primary"></i>Contact Information</h3></div>
            <div class="card-body company-detail-grid">
                <div><span>Company Email</span><strong>@if($company->company_email)<a href="mailto:{{ $company->company_email }}">{{ $company->company_email }}</a>@else-@endif</strong></div>
                <div><span>Phone</span><strong>@if($company->phone)<a href="tel:{{ $company->phone }}">{{ $company->phone }}</a>@else-@endif</strong></div>
                <div><span>Website</span><strong>@if($company->website)<a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer">{{ str($company->website)->after('//')->limit(36) }} <i class="ti ti-external-link"></i></a>@else-@endif</strong></div>
                <div><span>City</span><strong>{{ $company->city ?: '-' }}</strong></div>
                <div class="company-detail-wide"><span>Address</span><strong>{{ $company->address ?: '-' }}</strong></div>
            </div>
        </section>

        <section class="card company-detail-card">
            <div class="card-header"><h3 class="card-title mb-0"><i class="ti ti-id me-2 text-primary"></i>Authorized Person</h3></div>
            <div class="card-body company-detail-grid">
                <div><span>Name</span><strong>{{ $company->authorized_person ?: '-' }}</strong></div>
                <div><span>Designation</span><strong>{{ $company->designation ?: '-' }}</strong></div>
                <div><span>Manager / Contact</span><strong>{{ $company->manager_name ?: '-' }}</strong></div>
            </div>
        </section>

        <section class="card company-detail-card">
            <div class="card-header"><h3 class="card-title mb-0"><i class="ti ti-photo me-2 text-primary"></i>Brand Assets</h3></div>
            <div class="card-body company-detail-assets">
                <div><span>Company Logo</span>@if($company->logo_path)<img src="{{ asset('storage/'.$company->logo_path) }}" alt="Company logo">@else<p>No logo</p>@endif</div>
                <div><span>Signature</span>@if($company->signature_path)<img src="{{ asset('storage/'.$company->signature_path) }}" alt="Authorized signature">@else<p>No signature</p>@endif</div>
                <div><span>Stamp / Seal</span>@if($company->stamp_path)<img src="{{ asset('storage/'.$company->stamp_path) }}" alt="Company stamp">@else<p>No stamp</p>@endif</div>
            </div>
        </section>
    </div>

    <div class="col-xl-4">
        <section class="card company-detail-card">
            <div class="card-header"><h3 class="card-title mb-0"><i class="ti ti-chart-bar me-2 text-primary"></i>Quick Stats</h3></div>
            <div class="card-body company-detail-stats">
                <div><span>Total Users</span><strong>{{ $company->users->count() }}</strong></div>
                <div><span>Full Access</span><strong>{{ $company->users->where('company_role_num', 0)->count() }}</strong></div>
                <div><span>Active Users</span><strong>{{ $company->users->where('is_active', true)->count() }}</strong></div>
            </div>
        </section>

        <section class="card company-detail-card">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <h3 class="card-title mb-0"><i class="ti ti-users me-2 text-primary"></i>Company Admins</h3>
                @can('manage-branches')<a href="{{ route('users.create') }}" class="btn btn-sm btn-light"><i class="ti ti-plus me-1"></i>Add</a>@endcan
            </div>
            <div class="company-detail-users">
                @forelse($company->users as $member)
                    <div class="company-detail-user">
                        <span class="company-detail-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-grow-1"><strong class="d-block text-truncate">{{ $member->name }}</strong><small class="d-block text-truncate">{{ $member->email }}</small></div>
                        <span class="badge {{ (int)($member->company_role_num ?? 0) === 0 ? 'text-bg-success' : 'text-bg-warning' }}">{{ (int)($member->company_role_num ?? 0) === 0 ? 'Full' : 'Role '.$member->company_role_num }}</span>
                    </div>
                @empty
                    <p class="company-detail-empty">No users assigned yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
