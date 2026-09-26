@extends('layouts.app')
@section('title', ($template->exists ? 'Edit' : 'New') . ' Notification Template')
@section('page_title', ($template->exists ? 'Edit' : 'New') . ' Notification Template')

@push('styles')
<style>
/* ── Layout ────────────────────────────────────────────────── */
.form-wrap{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:24px;align-items:start}
@media(max-width:940px){.form-wrap{grid-template-columns:1fr}}

/* ── Card ──────────────────────────────────────────────────── */
.s-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(15,23,42,.06);margin-bottom:20px}
.s-card-head{padding:18px 22px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:14px}
.s-card-head .c-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;font-size:17px;flex:0 0 auto}
.s-card-head h3{margin:0;font-size:15px;font-weight:800;color:#0f172a}
.s-card-head p{margin:2px 0 0;font-size:12px;color:#94a3b8}
.s-card-body{padding:20px 22px}

/* ── Form fields ───────────────────────────────────────────── */
.fl{display:flex;flex-direction:column;gap:14px}
.fl label{display:block;font-size:11.5px;font-weight:700;color:#475569;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em}
.fl .hint{font-size:11px;color:#94a3b8;margin-top:4px}

/* ── Channel radio pills ───────────────────────────────────── */
.ch-picker{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
.ch-pill{position:relative;cursor:pointer}
.ch-pill input{position:absolute;opacity:0;width:0;height:0}
.ch-pill-inner{border:2px solid #e2e8f0;border-radius:10px;padding:10px 8px;text-align:center;transition:all .15s;background:#fff}
.ch-pill:hover .ch-pill-inner{border-color:#93c5fd;background:#f5f9ff}
.ch-pill input:checked + .ch-pill-inner{border-color:var(--ch-color);background:var(--ch-bg);box-shadow:0 0 0 3px color-mix(in srgb, var(--ch-color) 15%, transparent)}
.ch-pill-inner i{font-size:20px;display:block;margin-bottom:5px}
.ch-pill-inner span{font-size:11px;font-weight:700;color:#334155}
.ch-pill input:checked + .ch-pill-inner span{color:var(--ch-color)}

/* ── Textarea body ─────────────────────────────────────────── */
#bodyField{min-height:120px;font-size:13px;font-family:inherit;resize:vertical;line-height:1.6}

/* ── Variable chips ────────────────────────────────────────── */
.var-chips{display:flex;flex-wrap:wrap;gap:6px}
.var-chip{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:7px;font-size:11.5px;font-family:monospace;color:#334155;cursor:pointer;transition:all .15s;user-select:none}
.var-chip:hover{background:#eff6ff;border-color:#93c5fd;color:#2563eb}
.var-chip i{font-size:10px;color:#94a3b8}
.var-chip:hover i{color:#2563eb}

/* ── Icon picker ───────────────────────────────────────────── */
.icon-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:6px;max-height:200px;overflow-y:auto;padding:4px}
.icon-opt{width:100%;aspect-ratio:1;border:2px solid #e2e8f0;border-radius:8px;display:grid;place-items:center;font-size:18px;cursor:pointer;transition:all .15s;background:#fff;color:#475569}
.icon-opt:hover{border-color:#93c5fd;background:#eff6ff;color:#2563eb}
.icon-opt.selected{border-color:#2563eb;background:#eff6ff;color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}

/* ── Preview card ──────────────────────────────────────────── */
.preview-card{background:#1e293b;border-radius:20px;padding:16px;overflow:hidden}
.preview-phone{background:#0f172a;border-radius:14px;padding:12px}
.preview-notif{background:#fff;border-radius:12px;padding:12px 14px;display:flex;align-items:flex-start;gap:10px;box-shadow:0 4px 16px rgba(0,0,0,.25)}
.preview-notif-icon{width:36px;height:36px;border-radius:9px;display:grid;place-items:center;font-size:16px;flex:0 0 auto}
.preview-notif-content{flex:1;min-width:0}
.preview-notif-app{font-size:10px;color:#94a3b8;margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.05em}
.preview-notif-title{font-size:13px;font-weight:700;color:#0f172a;line-height:1.3;margin-bottom:3px}
.preview-notif-body{font-size:11.5px;color:#475569;line-height:1.5;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.preview-notif-time{font-size:10px;color:#94a3b8;margin-top:4px}
.preview-label{font-size:11px;font-weight:700;text-align:center;color:#475569;text-transform:uppercase;letter-spacing:.07em;margin-bottom:10px}

/* ── Toggle switch ─────────────────────────────────────────── */
.sw{position:relative;display:inline-block;width:42px;height:24px}
.sw input{opacity:0;width:0;height:0}
.sw-slider{position:absolute;inset:0;background:#cbd5e1;border-radius:999px;cursor:pointer;transition:.2s}
.sw-slider:before{content:'';position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 4px rgba(0,0,0,.18)}
.sw input:checked+.sw-slider{background:#2563eb}
.sw input:checked+.sw-slider:before{transform:translateX(18px)}
.sw-row{display:flex;align-items:center;gap:12px}
.sw-label{margin:0;font-size:13px;font-weight:600;color:#334155}

/* ── Breadcrumb ────────────────────────────────────────────── */
.bc{display:flex;align-items:center;gap:6px;font-size:12px;color:#94a3b8;margin-bottom:18px}
.bc a{color:#64748b;text-decoration:none;font-weight:600}
.bc a:hover{color:#2563eb}
.bc i{font-size:10px}

/* ── Flash ─────────────────────────────────────────────────── */
.flash{border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;margin-bottom:18px}
.flash.error{background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="bc">
    <a href="{{ route('settings.general') }}">Settings</a>
    <i class="ti ti-chevron-right"></i>
    <a href="{{ route('settings.notifications.index') }}">Notification Templates</a>
    <i class="ti ti-chevron-right"></i>
    <span>{{ $template->exists ? 'Edit Template' : 'New Template' }}</span>
</div>

@if($errors->any())
<div class="flash error"><i class="ti ti-alert-triangle-filled"></i> {{ $errors->first() }}</div>
@endif

@php
    $action  = $template->exists
        ? route('settings.notifications.update', $template)
        : route('settings.notifications.store');
    $method  = $template->exists ? 'PUT' : 'POST';

    $channelConfig = [
        'push'  => ['color'=>'#2563eb','bg'=>'#eff6ff','icon'=>'bi-bell-fill',      'label'=>'Push'],
        'sms'   => ['color'=>'#16a34a','bg'=>'#f0fdf4','icon'=>'bi-chat-dots-fill', 'label'=>'SMS'],
        'email' => ['color'=>'#ea580c','bg'=>'#fff7ed','icon'=>'bi-envelope-fill',  'label'=>'Email'],
        'all'   => ['color'=>'#7c3aed','bg'=>'#faf5ff','icon'=>'bi-broadcast-pin',  'label'=>'All'],
    ];

    $suggestedIcons = [
        'bi-bell','bi-bell-fill','bi-calendar-check','bi-calendar-x','bi-calendar-plus',
        'bi-alarm','bi-check-circle','bi-check2-circle','bi-x-circle','bi-exclamation-circle',
        'bi-car-front','bi-car-front-fill','bi-tools','bi-wrench-adjustable','bi-clipboard2-pulse',
        'bi-cash-stack','bi-cash-coin','bi-receipt','bi-bag-check','bi-cart-plus',
        'bi-person-check','bi-person-x','bi-key','bi-patch-check','bi-hand-thumbs-up',
        'bi-clock-history','bi-box-seam','bi-broadcast','bi-gear-wide-connected','bi-file-earmark-bar-graph',
    ];

    $currentChannel = old('channel', $template->channel ?? 'push');
    $currentIcon    = old('icon',    $template->icon    ?? 'bi-bell');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="form-wrap">

        {{-- ── LEFT: Main form ── --}}
        <div>

            {{-- Event & Basic Info --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#eff6ff;color:#2563eb"><i class="ti ti-bell"></i></div>
                    <div>
                        <h3>Template Details</h3>
                        <p>Event trigger, notification title and message body</p>
                    </div>
                </div>
                <div class="s-card-body">
                    <div class="fl">

                        {{-- Event Key (create only) --}}
                        @if(!$template->exists)
                        <div>
                            <label>Event <span class="text-danger">*</span></label>
                            <select class="form-select @error('event_key') is-invalid @enderror"
                                    name="event_key" id="eventKey" onchange="syncEventIcon()">
                                <option value="">— Choose event —</option>
                                @foreach($eventList as $key => $event)
                                <option value="{{ $key }}"
                                        data-icon="{{ $event['icon'] }}"
                                        {{ old('event_key', $selectedEvent ?? '') === $key ? 'selected' : '' }}>
                                    [{{ $event['category'] }}] {{ $event['label'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('event_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <span class="hint">Each event can have only one template</span>
                        </div>
                        @else
                        <div>
                            <label>Event</label>
                            <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px">
                                <i class="bi {{ $eventList[$template->event_key]['icon'] ?? 'bi-bell' }}" style="font-size:16px;color:#2563eb"></i>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:#0f172a">{{ $eventList[$template->event_key]['label'] ?? $template->event_key }}</div>
                                    <div style="font-size:11px;color:#94a3b8;font-family:monospace">{{ $template->event_key }}</div>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Title --}}
                        <div>
                            <label>Notification Title <span class="text-danger">*</span></label>
                            <input class="form-control @error('title') is-invalid @enderror"
                                   name="title" id="previewTitle"
                                   value="{{ old('title', $template->title ?? '') }}"
                                   placeholder="e.g. Booking Confirmed – {{booking_no}}"
                                   oninput="updatePreview()" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <span class="hint">Keep under 60 characters for best display</span>
                        </div>

                        {{-- Body --}}
                        <div>
                            <label>Message Body <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror"
                                      name="body" id="bodyField" id="previewBody"
                                      placeholder="Hi {{name}}, your booking {{booking_no}} has been confirmed…"
                                      oninput="updatePreview()" required>{{ old('body', $template->body ?? '') }}</textarea>
                            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <span class="hint">Use <code>{{"{{"}}variable{{"}}"}}</code> placeholders — click chips below to insert</span>
                        </div>

                        {{-- Variable chips --}}
                        <div>
                            <label>Available Variables</label>
                            <div class="var-chips">
                                @foreach($variables as $var => $desc)
                                <span class="var-chip" title="{{ $desc }}" onclick="insertVar('{{ $var }}')">
                                    <i class="ti ti-braces"></i>{{ $var }}
                                </span>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Channel --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#faf5ff;color:#7c3aed"><i class="ti ti-broadcast"></i></div>
                    <div>
                        <h3>Delivery Channel</h3>
                        <p>How this notification is sent</p>
                    </div>
                </div>
                <div class="s-card-body">
                    <div class="ch-picker">
                        @foreach($channelConfig as $ch => $cfg)
                        <label class="ch-pill" style="--ch-color:{{ $cfg['color'] }};--ch-bg:{{ $cfg['bg'] }}">
                            <input type="radio" name="channel" value="{{ $ch }}"
                                   {{ $currentChannel === $ch ? 'checked' : '' }}
                                   onchange="updatePreview()">
                            <div class="ch-pill-inner">
                                <i class="bi {{ $cfg['icon'] }}" style="color:{{ $cfg['color'] }}"></i>
                                <span>{{ $cfg['label'] }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Icon --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#f0fdf4;color:#16a34a"><i class="ti ti-stars"></i></div>
                    <div>
                        <h3>Notification Icon</h3>
                        <p>Icon shown alongside the notification</p>
                    </div>
                </div>
                <div class="s-card-body">
                    <input type="hidden" name="icon" id="iconValue" value="{{ $currentIcon }}">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
                        <div id="iconPreviewBadge"
                             style="width:44px;height:44px;border-radius:11px;background:#eff6ff;color:#2563eb;display:grid;place-items:center;font-size:22px;flex:0 0 auto">
                            <i class="bi {{ $currentIcon }}" id="iconPreviewI"></i>
                        </div>
                        <div style="font-size:13px;font-weight:600;color:#334155">
                            Selected: <code id="iconPreviewCode">{{ $currentIcon }}</code>
                        </div>
                    </div>
                    <div class="icon-grid" id="iconGrid">
                        @foreach($suggestedIcons as $ico)
                        <button type="button"
                                class="icon-opt {{ $currentIcon === $ico ? 'selected' : '' }}"
                                data-icon="{{ $ico }}"
                                title="{{ $ico }}"
                                onclick="selectIcon('{{ $ico }}')">
                            <i class="bi {{ $ico }}"></i>
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#fef3c7;color:#d97706"><i class="ti ti-adjustments"></i></div>
                    <div><h3>Status</h3><p>Enable or disable this template</p></div>
                </div>
                <div class="s-card-body">
                    <div class="sw-row">
                        <label class="sw">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" id="isActive"
                                   {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}>
                            <span class="sw-slider"></span>
                        </label>
                        <label class="sw-label" for="isActive">
                            Template Active
                            <span style="display:block;font-size:11px;color:#94a3b8;font-weight:400;margin-top:1px">
                                Inactive templates are skipped when events fire
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;gap:10px">
                <button class="btn btn-primary px-4">
                    <i class="ti ti-check me-1"></i>
                    {{ $template->exists ? 'Update Template' : 'Create Template' }}
                </button>
                <a href="{{ route('settings.notifications.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </div>

        </div>

        {{-- ── RIGHT: Preview & help ── --}}
        <div>

            {{-- Live preview --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#f8fafc;color:#64748b"><i class="ti ti-phone"></i></div>
                    <div><h3>Live Preview</h3><p>How the notification appears on device</p></div>
                </div>
                <div class="s-card-body">
                    <div class="preview-card">
                        <div class="preview-label" style="color:#94a3b8">Push Notification</div>
                        <div class="preview-phone">
                            <div class="preview-notif">
                                <div class="preview-notif-icon" id="previewIconBox" style="background:#eff6ff;color:#2563eb">
                                    <i class="bi {{ $currentIcon }}" id="previewIconEl"></i>
                                </div>
                                <div class="preview-notif-content">
                                    <div class="preview-notif-app">{{ $settings['site_name'] ?? $settings['business_name'] ?? 'BookingPro' }}</div>
                                    <div class="preview-notif-title" id="previewTitleEl">
                                        {{ $template->title ?? 'Notification Title' }}
                                    </div>
                                    <div class="preview-notif-body" id="previewBodyEl">
                                        {{ $template->body ?? 'Your notification message will appear here. Use variable chips to personalise.' }}
                                    </div>
                                    <div class="preview-notif-time">Just now</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Variable reference --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#faf5ff;color:#7c3aed"><i class="ti ti-code"></i></div>
                    <div><h3>Variable Reference</h3><p>Click any chip to insert into message body</p></div>
                </div>
                <div class="s-card-body">
                    <div style="display:flex;flex-direction:column;gap:7px">
                        @foreach($variables as $var => $desc)
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding:7px 10px;border-radius:8px;background:#f8fafc;cursor:pointer;transition:background .12s"
                             onclick="insertVar('{{ $var }}')"
                             onmouseover="this.style.background='#eff6ff'"
                             onmouseout="this.style.background='#f8fafc'">
                            <code style="font-size:12px;color:#2563eb;background:transparent;padding:0">{{ $var }}</code>
                            <span style="font-size:11px;color:#94a3b8;text-align:right">{{ $desc }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Tips --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="c-icon" style="background:#fef9c3;color:#ca8a04"><i class="ti ti-bulb"></i></div>
                    <div><h3>Tips</h3></div>
                </div>
                <div class="s-card-body">
                    <ul style="margin:0;padding-left:18px;font-size:12.5px;color:#475569;line-height:2">
                        <li>Keep titles under <strong>60 characters</strong></li>
                        <li>Keep body under <strong>150 characters</strong> for mobile</li>
                        <li>Always include <code>{{"{{"}}name{{"}}"}}</code> for personalisation</li>
                        <li>End with your business name using <code>{{"{{"}}company_name{{"}}"}}</code></li>
                        <li>Test with real events before going live</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
var bodyField   = document.getElementById('bodyField');
var titleInput  = document.getElementById('previewTitle');

/* ── Live preview update ───────────────────────────────────── */
function updatePreview() {
    var title = titleInput ? titleInput.value : '';
    var body  = bodyField  ? bodyField.value  : '';

    var t = document.getElementById('previewTitleEl');
    var b = document.getElementById('previewBodyEl');
    if(t) t.textContent = title || 'Notification Title';
    if(b) b.textContent = body  || 'Your notification message will appear here.';
}

/* ── Insert variable at cursor ─────────────────────────────── */
function insertVar(varStr) {
    if(!bodyField) return;
    var start = bodyField.selectionStart;
    var end   = bodyField.selectionEnd;
    var val   = bodyField.value;
    bodyField.value = val.substring(0, start) + varStr + val.substring(end);
    bodyField.selectionStart = bodyField.selectionEnd = start + varStr.length;
    bodyField.focus();
    updatePreview();
}

/* ── Icon selection ────────────────────────────────────────── */
function selectIcon(ico) {
    document.getElementById('iconValue').value = ico;
    document.getElementById('iconPreviewCode').textContent = ico;

    var previewI = document.getElementById('iconPreviewI');
    if(previewI){ previewI.className = 'bi ' + ico; }

    var previewEl = document.getElementById('previewIconEl');
    if(previewEl){ previewEl.className = 'bi ' + ico; }

    document.querySelectorAll('.icon-opt').forEach(function(b){
        b.classList.toggle('selected', b.dataset.icon === ico);
    });
}

/* ── Auto-fill icon when event changes (create mode) ─────── */
function syncEventIcon() {
    var sel  = document.getElementById('eventKey');
    if(!sel) return;
    var opt  = sel.options[sel.selectedIndex];
    var ico  = opt && opt.dataset.icon ? opt.dataset.icon : 'bi-bell';
    // map bi icon name (bootstrap icons class without bi-)
    selectIcon(ico);
}

/* ── Init preview ──────────────────────────────────────────── */
updatePreview();
</script>
@endpush
