@extends('layouts.app')
@section('title','Notification Templates')
@section('page_title','Notification Templates')

@push('styles')
<style>
/* ── Page header ───────────────────────────────────────────── */
.nt-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.nt-header-left h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
.nt-header-left p{margin:3px 0 0;font-size:13px;color:#64748b}

/* ── Stats bar ─────────────────────────────────────────────── */
.stats-bar{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.stat-card{background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 18px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 6px rgba(15,23,42,.05)}
.stat-icon{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;font-size:18px;flex:0 0 auto}
.stat-val{font-size:22px;font-weight:800;color:#0f172a;line-height:1}
.stat-lbl{font-size:11px;color:#94a3b8;margin-top:3px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
@media(max-width:860px){.stats-bar{grid-template-columns:repeat(2,1fr)}}

/* ── Filter bar ────────────────────────────────────────────── */
.filter-bar{display:flex;align-items:center;gap:10px;margin-bottom:16px;flex-wrap:wrap}
.filter-bar .search-wrap{position:relative;flex:1;min-width:200px}
.filter-bar .search-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px;pointer-events:none}
.filter-bar .search-wrap input{padding-left:34px}
.cat-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 13px;border-radius:99px;font-size:12px;font-weight:700;cursor:pointer;border:1.5px solid #e2e8f0;background:#fff;color:#475569;transition:all .15s;white-space:nowrap}
.cat-pill:hover{border-color:#93c5fd;color:#2563eb;background:#eff6ff}
.cat-pill.active{border-color:#2563eb;background:#2563eb;color:#fff}

/* ── Category group ────────────────────────────────────────── */
.cat-group{margin-bottom:28px}
.cat-title{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.cat-title h4{margin:0;font-size:13px;font-weight:800;color:#334155;text-transform:uppercase;letter-spacing:.08em}
.cat-title .cat-count{background:#f1f5f9;color:#64748b;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px}

/* ── Template card ─────────────────────────────────────────── */
.tpl-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:14px}
.tpl-card{background:#fff;border:1px solid #e2e8f0;border-radius:13px;overflow:hidden;transition:box-shadow .15s,border-color .15s;box-shadow:0 1px 5px rgba(15,23,42,.05)}
.tpl-card:hover{box-shadow:0 6px 24px rgba(15,23,42,.1);border-color:#c7d6f5}
.tpl-card.inactive{opacity:.62}

/* Card top band */
.tpl-band{height:4px}

/* Card body */
.tpl-body{padding:16px 18px 12px}
.tpl-top{display:flex;align-items:flex-start;gap:12px;margin-bottom:10px}
.tpl-icon{width:38px;height:38px;border-radius:9px;display:grid;place-items:center;font-size:17px;flex:0 0 auto}
.tpl-meta{flex:1;min-width:0}
.tpl-event{font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#94a3b8;margin-bottom:2px;font-family:monospace}
.tpl-title{font-size:13.5px;font-weight:700;color:#0f172a;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tpl-body-text{font-size:12px;color:#64748b;line-height:1.55;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;margin-bottom:12px}

/* Card footer */
.tpl-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;padding:10px 18px;border-top:1px solid #f1f5f9;background:#fafbfc}
.tpl-channel{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 10px;border-radius:99px}
.tpl-actions{display:flex;align-items:center;gap:4px}
.tpl-actions a,.tpl-actions button{width:30px;height:30px;border-radius:7px;display:grid;place-items:center;font-size:13px;border:1px solid #e2e8f0;background:#fff;color:#64748b;cursor:pointer;transition:all .15s;text-decoration:none}
.tpl-actions a:hover{background:#eff6ff;border-color:#93c5fd;color:#2563eb}
.tpl-actions .btn-toggle-on{background:#dcfce7;border-color:#86efac;color:#16a34a}
.tpl-actions .btn-toggle-off{background:#f1f5f9;border-color:#e2e8f0;color:#94a3b8}
.tpl-actions .btn-delete:hover{background:#fee2e2;border-color:#fca5a5;color:#dc2626}

/* ── Channel colors ────────────────────────────────────────── */
.ch-push  {background:#eff6ff;color:#2563eb}   .band-push  {background:#2563eb}
.ch-sms   {background:#f0fdf4;color:#16a34a}   .band-sms   {background:#16a34a}
.ch-email {background:#fff7ed;color:#ea580c}   .band-email {background:#ea580c}
.ch-all   {background:#faf5ff;color:#7c3aed}   .band-all   {background:#7c3aed}

/* ── Empty state ───────────────────────────────────────────── */
.empty-event{border:1.5px dashed #e2e8f0;border-radius:13px;padding:20px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;background:#fafbfc;transition:border-color .15s}
.empty-event:hover{border-color:#93c5fd;background:#f5f9ff}
.empty-event-left{display:flex;align-items:center;gap:12px}
.empty-event-icon{width:36px;height:36px;border-radius:9px;background:#f1f5f9;display:grid;place-items:center;font-size:16px;color:#94a3b8;flex:0 0 auto}
.empty-event-label{font-size:13px;font-weight:600;color:#475569}
.empty-event-key{font-size:11px;color:#94a3b8;font-family:monospace}

/* ── Flash ─────────────────────────────────────────────────── */
.flash{border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;margin-bottom:18px}
.flash.success{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.flash.error  {background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}
</style>
@endpush

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
