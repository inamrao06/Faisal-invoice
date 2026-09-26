@extends('layouts.app')
@section('title','General Settings')
@section('page_title','General Settings')

@push('styles')
<style>
/* ── Settings page shell ───────────────────────────────────── */
.settings-wrap{display:grid;grid-template-columns:220px minmax(0,1fr);gap:24px;align-items:start}
@media(max-width:860px){.settings-wrap{grid-template-columns:1fr}}

/* ── Left tab-nav ──────────────────────────────────────────── */
.tab-nav{background:#fff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(15,23,42,.06)}
.tab-nav-head{padding:16px 18px 12px;border-bottom:1px solid #f1f5f9}
.tab-nav-head h4{margin:0;font-size:12px;font-weight:800;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em}
.tab-nav a{
    display:flex;align-items:center;gap:11px;
    padding:11px 18px;font-size:13px;font-weight:600;color:#475569;
    border-left:3px solid transparent;transition:all .15s;text-decoration:none!important;
}
.tab-nav a:hover{background:#f8fafc;color:#0f172a}
.tab-nav a.active{background:#eff6ff;color:#2563eb;border-left-color:#2563eb}
.tab-nav a i{font-size:15px;width:18px;text-align:center}
.tab-nav a .nav-badge{margin-left:auto;background:#f1f5f9;color:#64748b;font-size:10px;font-weight:700;padding:2px 7px;border-radius:99px}
.tab-nav a.active .nav-badge{background:#dbeafe;color:#2563eb}

/* ── Tab panels ────────────────────────────────────────────── */
.tab-panel{display:none}
.tab-panel.active{display:block}

/* ── Card ──────────────────────────────────────────────────── */
.s-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 2px 12px rgba(15,23,42,.06);overflow:hidden;margin-bottom:20px}
.s-card-head{padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;gap:14px}
.s-card-head .card-icon{width:38px;height:38px;border-radius:10px;display:grid;place-items:center;font-size:17px;flex:0 0 auto}
.s-card-head h3{margin:0;font-size:15px;font-weight:800;color:#0f172a}
.s-card-head p{margin:2px 0 0;font-size:12px;color:#94a3b8}
.s-card-body{padding:22px 24px}

/* ── Form ──────────────────────────────────────────────────── */
.fg{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 18px}
.fg .span-2{grid-column:span 2}
.fg label{display:block;font-size:11.5px;font-weight:700;color:#475569;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em}
.fg .hint{display:block;font-size:11px;color:#94a3b8;margin-top:4px}
.form-footer{display:flex;align-items:center;gap:10px;padding:18px 24px;border-top:1px solid #f1f5f9;background:#fafbfc}

/* ── Logo preview ──────────────────────────────────────────── */
.logo-box{display:flex;align-items:center;gap:16px;padding:14px 16px;background:#f8fafc;border:1px dashed #cbd5e1;border-radius:10px;margin-top:8px}
.logo-box img{height:48px;width:auto;border-radius:6px;object-fit:contain}
.logo-box .remove-logo{font-size:11px;color:#ef4444;text-decoration:none}

/* ── Section separator ─────────────────────────────────────── */
.section-sep{border:none;border-top:1px dashed #e2e8f0;margin:20px 0}

/* ── Provider card pick ────────────────────────────────────── */
.provider-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.provider-card{border:2px solid #e2e8f0;border-radius:10px;padding:14px 12px;cursor:pointer;text-align:center;transition:all .15s;background:#fff}
.provider-card:hover{border-color:#93c5fd;background:#eff6ff}
.provider-card.selected{border-color:#2563eb;background:#eff6ff;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.provider-card i{font-size:26px;display:block;margin-bottom:6px}
.provider-card span{font-size:12px;font-weight:700;color:#334155}
.provider-card input{display:none}

/* ── Env badge ─────────────────────────────────────────────── */
.env-badge{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;padding:3px 9px;border-radius:99px;margin-bottom:4px}
.env-badge.local{background:#dcfce7;color:#15803d}
.env-badge.staging{background:#fef9c3;color:#854d0e}
.env-badge.production{background:#fee2e2;color:#b91c1c}

/* ── Toggle switch ─────────────────────────────────────────── */
.sw{position:relative;display:inline-block;width:42px;height:24px}
.sw input{opacity:0;width:0;height:0}
.sw-slider{position:absolute;inset:0;background:#cbd5e1;border-radius:999px;cursor:pointer;transition:.2s}
.sw-slider:before{content:'';position:absolute;height:18px;width:18px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.2s;box-shadow:0 1px 4px rgba(0,0,0,.18)}
.sw input:checked+.sw-slider{background:#2563eb}
.sw input:checked+.sw-slider:before{transform:translateX(18px)}
.sw-row{display:flex;align-items:center;gap:12px}
.sw-row label.sw-label{margin:0;font-size:13px;font-weight:600;color:#334155;text-transform:none;letter-spacing:0}

/* ── Alert ─────────────────────────────────────────────────── */
.flash{border-radius:10px;padding:12px 16px;display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600;margin-bottom:18px}
.flash.success{background:#dcfce7;color:#15803d;border:1px solid #bbf7d0}
.flash.error{background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}
</style>
@endpush

@section('content')

@if(session('success'))
<div class="flash success"><i class="ti ti-circle-check-filled"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="flash error"><i class="ti ti-alert-triangle-filled"></i> {{ $errors->first() }}</div>
@endif

<div class="settings-wrap">

    {{-- ── LEFT NAV ── --}}
    <div class="tab-nav">
        <div class="tab-nav-head"><h4>Settings</h4></div>
        <a href="#tab-app"  class="tab-link active"  data-tab="tab-app">
            <i class="ti ti-info-circle"></i> App Info
        </a>
        <a href="#tab-mail" class="tab-link" data-tab="tab-mail">
            <i class="ti ti-mail"></i> Mail / SMTP
        </a>
        <a href="#tab-push" class="tab-link" data-tab="tab-push">
            <i class="ti ti-bell"></i> Push Notifications
        </a>
        <a href="#tab-env"  class="tab-link" data-tab="tab-env">
            <i class="ti ti-terminal"></i> Environment
            @php $envBadge = $env['APP_ENV'] ?? 'local'; @endphp
            <span class="nav-badge">{{ $envBadge }}</span>
        </a>
    </div>

    {{-- ── RIGHT PANELS ── --}}
    <div>

        {{-- ════════════════════════════════════════════════════
             TAB 1 — APP INFO
        ════════════════════════════════════════════════════ --}}
        <div id="tab-app" class="tab-panel active">
            <form method="POST" enctype="multipart/form-data" action="{{ route('settings.general.update') }}">
                @csrf

                {{-- Identity --}}
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#eff6ff;color:#2563eb"><i class="ti ti-building"></i></div>
                        <div>
                            <h3>Business Identity</h3>
                            <p>Site name, slug and contact information</p>
                        </div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div>
                                <label>Site / Business Name <span class="text-danger">*</span></label>
                                <input class="form-control @error('site_name') is-invalid @enderror"
                                       name="site_name"
                                       value="{{ old('site_name', $settings['site_name'] ?? $settings['business_name'] ?? '') }}"
                                       placeholder="e.g. Head Office Motors"
                                       required>
                                @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <span class="hint">Shown in browser title, sidebar and emails</span>
                            </div>
                            <div>
                                <label>Slug / Short Code <span class="text-danger">*</span></label>
                                <input class="form-control @error('slug') is-invalid @enderror"
                                       name="slug"
                                       value="{{ old('slug', $settings['slug'] ?? '') }}"
                                       placeholder="e.g. head-office-motors"
                                       required>
                                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label>Contact Number</label>
                                <input class="form-control" name="number"
                                       value="{{ old('number', $settings['number'] ?? '') }}"
                                       placeholder="+92 300 0000000">
                            </div>
                            <div>
                                <label>Contact Email</label>
                                <input class="form-control" type="email" name="email"
                                       value="{{ old('email', $settings['email'] ?? '') }}"
                                       placeholder="info@example.com">
                            </div>
                            <div class="span-2">
                                <label>Business Address</label>
                                <input class="form-control" name="address"
                                       value="{{ old('address', $settings['address'] ?? '') }}"
                                       placeholder="Street, City, Country">
                            </div>
                            <div class="span-2">
                                <label>Tagline / Description</label>
                                <input class="form-control" name="tagline"
                                       value="{{ old('tagline', $settings['tagline'] ?? '') }}"
                                       placeholder="Short tagline shown under site name">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Logo & Favicon --}}
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#faf5ff;color:#7c3aed"><i class="ti ti-photo"></i></div>
                        <div>
                            <h3>Branding</h3>
                            <p>Logo and favicon displayed across the app</p>
                        </div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div>
                                <label>Logo</label>
                                <input class="form-control" type="file" name="logo" accept="image/*">
                                <span class="hint">JPG, PNG, WEBP or SVG — max 2 MB</span>
                                @if(!empty($settings['logo_path']))
                                <div class="logo-box mt-2">
                                    <img src="{{ asset('storage/'.$settings['logo_path']) }}" alt="Logo">
                                    <div>
                                        <div style="font-size:12px;font-weight:600;color:#334155">Current logo</div>
                                        <div style="font-size:11px;color:#94a3b8">Upload a new file to replace it</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div>
                                <label>Favicon</label>
                                <input class="form-control" type="file" name="favicon" accept="image/*,.ico">
                                <span class="hint">ICO, PNG or JPG — max 512 KB · 32×32 recommended</span>
                                @if(!empty($settings['favicon_path']))
                                <div class="logo-box mt-2">
                                    <img src="{{ asset('storage/'.$settings['favicon_path']) }}" alt="Favicon" style="height:32px">
                                    <div>
                                        <div style="font-size:12px;font-weight:600;color:#334155">Current favicon</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Locale & Format --}}
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#f0fdf4;color:#16a34a"><i class="ti ti-world"></i></div>
                        <div>
                            <h3>Locale & Formats</h3>
                            <p>Timezone, date format and language preferences</p>
                        </div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div>
                                <label>Timezone</label>
                                <input class="form-control" name="timezone"
                                       value="{{ old('timezone', $settings['timezone'] ?? $env['APP_TIMEZONE'] ?? 'UTC') }}"
                                       placeholder="Asia/Karachi">
                                <span class="hint">PHP timezone identifier</span>
                            </div>
                            <div>
                                <label>Date Format</label>
                                <select class="form-select" name="date_format">
                                    @foreach(['d/m/Y'=>'DD/MM/YYYY', 'm/d/Y'=>'MM/DD/YYYY', 'Y-m-d'=>'YYYY-MM-DD', 'd M Y'=>'DD Mon YYYY'] as $fmt => $label)
                                    <option value="{{ $fmt }}" @selected(($settings['date_format'] ?? 'd/m/Y') === $fmt)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button class="btn btn-primary px-4"><i class="ti ti-check me-1"></i>Save App Info</button>
                    <span style="font-size:12px;color:#94a3b8">Changes also update APP_NAME in .env</span>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════════════════════
             TAB 2 — MAIL
        ════════════════════════════════════════════════════ --}}
        <div id="tab-mail" class="tab-panel">
            <form method="POST" action="{{ route('settings.general.mail') }}">
                @csrf

                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#fff7ed;color:#ea580c"><i class="ti ti-mail"></i></div>
                        <div>
                            <h3>Mail / SMTP Configuration</h3>
                            <p>Settings are written directly to your <code>.env</code> file</p>
                        </div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div class="span-2">
                                <label>Mail Driver</label>
                                <select class="form-select" name="mail_mailer" id="mailMailer">
                                    @foreach(['smtp'=>'SMTP','sendmail'=>'Sendmail','mailgun'=>'Mailgun','ses'=>'Amazon SES','log'=>'Log (Development)','array'=>'Array (Testing)'] as $v => $l)
                                    <option value="{{ $v }}" @selected(($settings['mail_mailer'] ?? $env['MAIL_MAILER'] ?? 'log') === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="smtpFields" class="span-2 fg" style="grid-column:span 2;padding:0;gap:14px 18px">
                                <div>
                                    <label>SMTP Host</label>
                                    <input class="form-control" name="mail_host"
                                           value="{{ old('mail_host', $settings['mail_host'] ?? $env['MAIL_HOST'] ?? '') }}"
                                           placeholder="smtp.mailtrap.io">
                                </div>
                                <div>
                                    <label>SMTP Port</label>
                                    <input class="form-control" name="mail_port" type="number"
                                           value="{{ old('mail_port', $settings['mail_port'] ?? $env['MAIL_PORT'] ?? '587') }}"
                                           placeholder="587">
                                </div>
                                <div>
                                    <label>Username</label>
                                    <input class="form-control" name="mail_username"
                                           value="{{ old('mail_username', $settings['mail_username'] ?? $env['MAIL_USERNAME'] ?? '') }}"
                                           placeholder="SMTP username">
                                </div>
                                <div>
                                    <label>Password</label>
                                    <input class="form-control" type="password" name="mail_password"
                                           value="{{ old('mail_password', $settings['mail_password'] ?? $env['MAIL_PASSWORD'] ?? '') }}"
                                           placeholder="SMTP password"
                                           autocomplete="new-password">
                                </div>
                                <div>
                                    <label>Encryption</label>
                                    <select class="form-select" name="mail_encryption">
                                        @foreach(['' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'] as $v => $l)
                                        <option value="{{ $v }}" @selected(($settings['mail_encryption'] ?? $env['MAIL_ENCRYPTION'] ?? 'tls') === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div></div>
                            </div>

                            <hr class="section-sep span-2">

                            <div>
                                <label>From Address</label>
                                <input class="form-control" type="email" name="mail_from_address"
                                       value="{{ old('mail_from_address', $settings['mail_from_address'] ?? $env['MAIL_FROM_ADDRESS'] ?? '') }}"
                                       placeholder="noreply@example.com">
                                <span class="hint">The "from" address in outgoing emails</span>
                            </div>
                            <div>
                                <label>From Name</label>
                                <input class="form-control" name="mail_from_name"
                                       value="{{ old('mail_from_name', $settings['mail_from_name'] ?? $env['MAIL_FROM_NAME'] ?? '') }}"
                                       placeholder="BookingPro">
                            </div>
                        </div>

                        {{-- Current .env values --}}
                        <div style="margin-top:20px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px">
                            <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#94a3b8;margin-bottom:10px;letter-spacing:.07em">Current .env values</div>
                            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px">
                                @foreach(['MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_FROM_ADDRESS'] as $k)
                                <div style="font-size:12px;color:#475569"><span style="color:#94a3b8;font-family:monospace">{{ $k }}</span> = <strong>{{ $env[$k] ?? '—' }}</strong></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button class="btn btn-primary px-4"><i class="ti ti-check me-1"></i>Save Mail Settings</button>
                    <span style="font-size:12px;color:#94a3b8">Writes to .env and clears config cache</span>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════════════════════
             TAB 3 — PUSH NOTIFICATIONS
        ════════════════════════════════════════════════════ --}}
        <div id="tab-push" class="tab-panel">
            <form method="POST" action="{{ route('settings.general.push') }}">
                @csrf

                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#eff6ff;color:#2563eb"><i class="ti ti-bell-filled"></i></div>
                        <div>
                            <h3>Push Notification Provider</h3>
                            <p>Choose your provider and enter API credentials</p>
                        </div>
                    </div>
                    <div class="s-card-body">

                        {{-- Provider picker --}}
                        <label style="font-size:11.5px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:10px">Select Provider</label>
                        <div class="provider-grid" style="margin-bottom:24px">
                            @php $currentProvider = $settings['push_provider'] ?? $env['PUSH_PROVIDER'] ?? ''; @endphp
                            @foreach([
                                'firebase'  => ['label'=>'Firebase FCM',  'icon'=>'bi-fire',        'color'=>'#f97316'],
                                'onesignal' => ['label'=>'OneSignal',     'icon'=>'bi-broadcast',   'color'=>'#e11d48'],
                                'pusher'    => ['label'=>'Pusher Beams',  'icon'=>'bi-lightning-charge-fill', 'color'=>'#7c3aed'],
                            ] as $pKey => $p)
                            <label class="provider-card {{ $currentProvider === $pKey ? 'selected' : '' }}" for="prov_{{ $pKey }}">
                                <input type="radio" name="push_provider" id="prov_{{ $pKey }}" value="{{ $pKey }}"
                                       {{ $currentProvider === $pKey ? 'checked' : '' }}
                                       onchange="switchProvider('{{ $pKey }}')">
                                <i class="{{ $p['icon'] }}" style="color:{{ $p['color'] }}"></i>
                                <span>{{ $p['label'] }}</span>
                            </label>
                            @endforeach
                        </div>

                        {{-- Firebase fields --}}
                        <div id="fields-firebase" class="provider-fields {{ ($currentProvider === 'firebase' || !$currentProvider) ? '' : 'd-none' }}">
                            <div class="fg">
                                <div class="span-2">
                                    <label>Server Key (Legacy)</label>
                                    <input class="form-control font-monospace" name="firebase_server_key"
                                           value="{{ old('firebase_server_key', $settings['firebase_server_key'] ?? $env['FIREBASE_SERVER_KEY'] ?? '') }}"
                                           placeholder="AAAAxxxxxxx:APA91b…">
                                    <span class="hint">From Firebase Console → Project Settings → Cloud Messaging</span>
                                </div>
                                <div>
                                    <label>Sender ID</label>
                                    <input class="form-control font-monospace" name="firebase_sender_id"
                                           value="{{ old('firebase_sender_id', $settings['firebase_sender_id'] ?? $env['FIREBASE_SENDER_ID'] ?? '') }}"
                                           placeholder="1234567890">
                                </div>
                                <div>
                                    <label>VAPID Public Key (Web Push)</label>
                                    <input class="form-control font-monospace" name="firebase_vapid_key"
                                           value="{{ old('firebase_vapid_key', $settings['firebase_vapid_key'] ?? $env['FIREBASE_VAPID_KEY'] ?? '') }}"
                                           placeholder="BNxxxxxx…">
                                </div>
                            </div>
                        </div>

                        {{-- OneSignal fields --}}
                        <div id="fields-onesignal" class="provider-fields {{ $currentProvider === 'onesignal' ? '' : 'd-none' }}">
                            <div class="fg">
                                <div>
                                    <label>App ID</label>
                                    <input class="form-control font-monospace" name="onesignal_app_id"
                                           value="{{ old('onesignal_app_id', $settings['onesignal_app_id'] ?? $env['ONESIGNAL_APP_ID'] ?? '') }}"
                                           placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">
                                </div>
                                <div>
                                    <label>REST API Key</label>
                                    <input class="form-control font-monospace" name="onesignal_api_key"
                                           value="{{ old('onesignal_api_key', $settings['onesignal_api_key'] ?? $env['ONESIGNAL_API_KEY'] ?? '') }}"
                                           placeholder="Basic xxxxxxx…">
                                </div>
                            </div>
                        </div>

                        {{-- Pusher fields --}}
                        <div id="fields-pusher" class="provider-fields {{ $currentProvider === 'pusher' ? '' : 'd-none' }}">
                            <div class="fg">
                                <div>
                                    <label>App ID</label>
                                    <input class="form-control font-monospace" name="pusher_app_id"
                                           value="{{ old('pusher_app_id', $settings['pusher_app_id'] ?? $env['PUSHER_APP_ID'] ?? '') }}"
                                           placeholder="123456">
                                </div>
                                <div>
                                    <label>App Key</label>
                                    <input class="form-control font-monospace" name="pusher_app_key"
                                           value="{{ old('pusher_app_key', $settings['pusher_app_key'] ?? $env['PUSHER_APP_KEY'] ?? '') }}"
                                           placeholder="app-key">
                                </div>
                                <div>
                                    <label>App Secret</label>
                                    <input class="form-control font-monospace" type="password" name="pusher_app_secret"
                                           value="{{ old('pusher_app_secret', $settings['pusher_app_secret'] ?? $env['PUSHER_APP_SECRET'] ?? '') }}"
                                           placeholder="app-secret" autocomplete="new-password">
                                </div>
                                <div>
                                    <label>Cluster</label>
                                    <input class="form-control" name="pusher_app_cluster"
                                           value="{{ old('pusher_app_cluster', $settings['pusher_app_cluster'] ?? $env['PUSHER_APP_CLUSTER'] ?? 'ap2') }}"
                                           placeholder="ap2">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Quick link to templates --}}
                <div class="s-card">
                    <div class="s-card-body" style="display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 20px">
                        <div style="display:flex;align-items:center;gap:12px">
                            <div style="width:36px;height:36px;border-radius:9px;background:#eff6ff;color:#2563eb;display:grid;place-items:center;font-size:16px">
                                <i class="ti ti-list-details"></i>
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:700;color:#0f172a">Notification Templates</div>
                                <div style="font-size:12px;color:#94a3b8">Manage message templates for each event type</div>
                            </div>
                        </div>
                        <a href="{{ route('settings.notifications.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-arrow-right me-1"></i>Manage Templates
                        </a>
                    </div>
                </div>

                <div class="form-footer">
                    <button class="btn btn-primary px-4"><i class="ti ti-check me-1"></i>Save Push Settings</button>
                    <span style="font-size:12px;color:#94a3b8">Credentials written to .env file</span>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════════════════════
             TAB 4 — ENVIRONMENT
        ════════════════════════════════════════════════════ --}}
        <div id="tab-env" class="tab-panel">
            <form method="POST" action="{{ route('settings.general.env') }}">
                @csrf

                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#f0fdf4;color:#16a34a"><i class="ti ti-terminal"></i></div>
                        <div>
                            <h3>Application Environment</h3>
                            <p>Core .env values — changes clear config cache automatically</p>
                        </div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div class="span-2">
                                <label>App URL <span class="text-danger">*</span></label>
                                <input class="form-control font-monospace" name="app_url" type="url"
                                       value="{{ old('app_url', $env['APP_URL'] ?? config('app.url')) }}"
                                       placeholder="https://example.com" required>
                                <span class="hint">Full URL with scheme — no trailing slash</span>
                            </div>
                            <div>
                                <label>Environment</label>
                                <select class="form-select" name="app_env" id="appEnvSelect">
                                    @foreach(['local'=>'Local (Development)','staging'=>'Staging','production'=>'Production'] as $v => $l)
                                    <option value="{{ $v }}" @selected(($env['APP_ENV'] ?? 'local') === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>App Locale</label>
                                <input class="form-control" name="app_locale"
                                       value="{{ old('app_locale', $env['APP_LOCALE'] ?? 'en') }}"
                                       placeholder="en">
                            </div>
                            <div>
                                <label>Timezone</label>
                                <input class="form-control" name="app_timezone"
                                       value="{{ old('app_timezone', $env['APP_TIMEZONE'] ?? 'UTC') }}"
                                       placeholder="Asia/Karachi">
                            </div>
                            <div>
                                <div class="sw-row">
                                    <label class="sw">
                                        <input type="hidden" name="app_debug" value="0">
                                        <input type="checkbox" name="app_debug" value="1"
                                               id="debugToggle"
                                               {{ ($env['APP_DEBUG'] ?? 'false') === 'true' ? 'checked' : '' }}>
                                        <span class="sw-slider"></span>
                                    </label>
                                    <label class="sw-label" for="debugToggle">
                                        Debug Mode
                                        <span style="display:block;font-size:11px;color:#94a3b8;font-weight:400">Show detailed error messages</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- ENV snapshot --}}
                        <div style="margin-top:22px;background:#0f172a;border-radius:12px;padding:18px 20px">
                            <div style="font-size:11px;font-weight:800;text-transform:uppercase;color:#475569;margin-bottom:12px;letter-spacing:.07em">.env snapshot</div>
                            @foreach(['APP_NAME','APP_ENV','APP_URL','APP_DEBUG','APP_TIMEZONE','APP_LOCALE',
                                      'DB_CONNECTION','MAIL_MAILER','QUEUE_CONNECTION','FILESYSTEM_DISK'] as $k)
                            @if(isset($env[$k]))
                            <div style="display:flex;gap:8px;font-size:12px;font-family:monospace;margin-bottom:4px">
                                <span style="color:#7dd3fc;min-width:200px">{{ $k }}</span>
                                <span style="color:#6b7280">=</span>
                                <span style="color:#86efac">{{ Str::limit($env[$k], 60) }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>

                        {{-- Production warning --}}
                        <div id="prodWarning" style="display:none;margin-top:16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;font-size:13px;color:#b91c1c">
                            <i class="ti ti-alert-triangle-filled me-2"></i>
                            <strong>Production mode:</strong> Make sure <code>APP_DEBUG</code> is <strong>false</strong> and all secrets are properly set before deploying.
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button class="btn btn-primary px-4"><i class="ti ti-check me-1"></i>Save Environment</button>
                    <span style="font-size:12px;color:#94a3b8">Runs <code>php artisan config:clear</code> after save</span>
                </div>
            </form>
        </div>

    </div>{{-- end right panels --}}
</div>

@endsection

@push('scripts')
<script>
/* ── Tab switching ─────────────────────────────────────────── */
document.querySelectorAll('.tab-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var tab = this.dataset.tab;

        document.querySelectorAll('.tab-link').forEach(function(l){ l.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });

        this.classList.add('active');
        document.getElementById(tab).classList.add('active');

        // Persist tab in URL hash
        history.replaceState(null, '', '#' + tab);
    });
});

/* Restore tab from hash on load */
(function(){
    var hash = location.hash.replace('#','');
    if(hash && document.getElementById(hash)){
        document.querySelectorAll('.tab-link').forEach(function(l){ l.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });
        document.querySelector('[data-tab="'+hash+'"]').classList.add('active');
        document.getElementById(hash).classList.add('active');
    }
})();

/* ── Push provider switcher ────────────────────────────────── */
function switchProvider(key) {
    document.querySelectorAll('.provider-fields').forEach(function(el){ el.classList.add('d-none'); });
    document.querySelectorAll('.provider-card').forEach(function(el){ el.classList.remove('selected'); });
    var el = document.getElementById('fields-' + key);
    if(el) el.classList.remove('d-none');
    var card = document.querySelector('#prov_' + key).closest('.provider-card');
    if(card) card.classList.add('selected');
}

/* ── ENV: production warning ───────────────────────────────── */
var envSel = document.getElementById('appEnvSelect');
function checkEnv(){
    var warn = document.getElementById('prodWarning');
    if(warn) warn.style.display = envSel && envSel.value === 'production' ? 'block' : 'none';
}
if(envSel){ envSel.addEventListener('change', checkEnv); checkEnv(); }

/* ── Mail: hide/show SMTP fields ───────────────────────────── */
var mailSel = document.getElementById('mailMailer');
function checkMailer(){
    var smtp = document.getElementById('smtpFields');
    if(!smtp) return;
    var show = mailSel && ['smtp','sendmail'].includes(mailSel.value);
    smtp.style.opacity = show ? '1' : '.4';
}
if(mailSel){ mailSel.addEventListener('change', checkMailer); checkMailer(); }
</script>
@endpush
