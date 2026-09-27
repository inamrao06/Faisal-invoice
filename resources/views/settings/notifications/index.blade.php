@extends('layouts.app')
@section('title','Notification Templates')
@section('page_title','Notification Templates')


@section('content')

@if(session('success'))
<div class="flash success"><i class="ti ti-circle-check-filled"></i> {{ session('success') }}</div>
@endif

{{-- ── Page header ── --}}
<div class="nt-header">
    <div class="nt-header-left">
        <h2><i class="ti ti-bell-filled me-2" style="color:#2563eb"></i>Notification Templates</h2>
        <p>Manage push, SMS and email message templates for every event in the system</p>
    </div>
    <div style="display:flex;gap:8px">
        <a href="{{ route('settings.general') }}#tab-push" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-settings me-1"></i>Push Settings
        </a>
        <a href="{{ route('settings.notifications.create') }}" class="btn btn-primary btn-sm">
            <i class="ti ti-plus me-1"></i>New Template
        </a>
    </div>
</div>

{{-- ── Stats ── --}}
@php
    $total    = $templates->count();
    $active   = $templates->where('is_active', true)->count();
    $inactive = $total - $active;
    $covered  = $templates->keys()->unique()->count();
    $allEvents= count($eventList);
@endphp
<div class="stats-bar">
    <div class="stat-card">
        <div class="stat-icon" style="background:#eff6ff;color:#2563eb"><i class="ti ti-list-details"></i></div>
        <div><div class="stat-val">{{ $total }}</div><div class="stat-lbl">Total Templates</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;color:#16a34a"><i class="ti ti-toggle-right"></i></div>
        <div><div class="stat-val">{{ $active }}</div><div class="stat-lbl">Active</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;color:#d97706"><i class="ti ti-alert-circle"></i></div>
        <div><div class="stat-val">{{ $allEvents - $covered }}</div><div class="stat-lbl">Not Configured</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#faf5ff;color:#7c3aed"><i class="ti ti-broadcast"></i></div>
        <div><div class="stat-val">{{ $covered }}</div><div class="stat-lbl">Events Covered</div></div>
    </div>
</div>

{{-- ── Filter bar ── --}}
<div class="filter-bar">
    <div class="search-wrap">
        <i class="ti ti-search"></i>
        <input class="form-control form-control-sm" id="tplSearch" placeholder="Search templates…">
    </div>
    <button class="cat-pill active" data-cat="all">All</button>
    @foreach($grouped->keys() as $cat)
    <button class="cat-pill" data-cat="{{ Str::slug($cat) }}">{{ $cat }}</button>
    @endforeach
</div>

{{-- ── Template groups ── --}}
<div id="tplList">
@foreach($grouped as $category => $events)
<div class="cat-group" data-category="{{ Str::slug($category) }}">
    <div class="cat-title">
        <h4>{{ $category }}</h4>
        <span class="cat-count">{{ count($events) }} events</span>
    </div>

    <div class="tpl-grid">
    @foreach($events as $key => $event)
    @php $tpl = $templates->get($key); @endphp

    @if($tpl)
    {{-- Configured template card --}}
    <div class="tpl-card {{ $tpl->is_active ? '' : 'inactive' }} tpl-item" data-name="{{ strtolower($tpl->title . ' ' . $key) }}">
        <div class="tpl-band band-{{ $tpl->channel }}"></div>
        <div class="tpl-body">
            <div class="tpl-top">
                <div class="tpl-icon ch-{{ $tpl->channel }}">
                    <i class="bi {{ $event['icon'] }}"></i>
                </div>
                <div class="tpl-meta">
                    <div class="tpl-event">{{ $key }}</div>
                    <div class="tpl-title" title="{{ $tpl->title }}">{{ $tpl->title }}</div>
                </div>
            </div>
            <div class="tpl-body-text">{{ $tpl->body }}</div>
        </div>
        <div class="tpl-footer">
            <span class="tpl-channel ch-{{ $tpl->channel }}">
                <i class="bi {{ $channels[$tpl->channel]['icon'] ?? 'bi-bell' }}"></i>
                {{ ucfirst($tpl->channel) }}
            </span>
            <div class="tpl-actions">
                {{-- Toggle --}}
                <form method="POST" action="{{ route('settings.notifications.toggle', $tpl) }}" style="display:contents">
                    @csrf
                    <button type="submit"
                            class="{{ $tpl->is_active ? 'btn-toggle-on' : 'btn-toggle-off' }}"
                            title="{{ $tpl->is_active ? 'Disable' : 'Enable' }}">
                        <i class="bi {{ $tpl->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                    </button>
                </form>
                {{-- Edit --}}
                <a href="{{ route('settings.notifications.edit', $tpl) }}" title="Edit">
                    <i class="ti ti-pencil"></i>
                </a>
                {{-- Delete --}}
                <form method="POST" action="{{ route('settings.notifications.destroy', $tpl) }}" style="display:contents"
                      onsubmit="return confirm('Delete this template?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-delete" title="Delete">
                        <i class="ti ti-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @else
    {{-- Empty / unconfigured event --}}
    <div class="empty-event tpl-item" data-name="{{ strtolower($event['label'] . ' ' . $key) }}">
        <div class="empty-event-left">
            <div class="empty-event-icon"><i class="bi {{ $event['icon'] }}"></i></div>
            <div>
                <div class="empty-event-label">{{ $event['label'] }}</div>
                <div class="empty-event-key">{{ $key }}</div>
            </div>
        </div>
        <a href="{{ route('settings.notifications.create', ['event' => $key]) }}"
           class="btn btn-sm btn-outline-primary" style="white-space:nowrap">
            <i class="ti ti-plus me-1"></i>Add
        </a>
    </div>
    @endif

    @endforeach
    </div>
</div>
@endforeach
</div>

@endsection

@push('scripts')
<script>
/* ── Category filter ───────────────────────────────────────── */
document.querySelectorAll('.cat-pill').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.querySelectorAll('.cat-pill').forEach(function(b){ b.classList.remove('active'); });
        this.classList.add('active');
        var cat = this.dataset.cat;
        document.querySelectorAll('.cat-group').forEach(function(g){
            g.style.display = (cat === 'all' || g.dataset.category === cat) ? '' : 'none';
        });
    });
});

/* ── Live search ───────────────────────────────────────────── */
document.getElementById('tplSearch').addEventListener('input', function(){
    var q = this.value.toLowerCase().trim();
    document.querySelectorAll('.tpl-item').forEach(function(item){
        item.style.display = (!q || item.dataset.name.includes(q)) ? '' : 'none';
    });
    /* hide empty category headings */
    document.querySelectorAll('.cat-group').forEach(function(g){
        var visible = Array.from(g.querySelectorAll('.tpl-item')).some(function(i){ return i.style.display !== 'none'; });
        g.style.display = visible ? '' : 'none';
    });
});
</script>
@endpush
