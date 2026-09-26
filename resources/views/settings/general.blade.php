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
.logo-box strong,.logo-box small{display:block;font-size:12px;color:var(--bs-body-color)}
.logo-box small{font-size:11px;color:var(--bs-secondary-color)}

/* ── Section separator ─────────────────────────────────────── */
.section-sep{border:none;border-top:1px dashed #e2e8f0;margin:20px 0}

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
                        <div><h3>Branding</h3><p>Logo and favicon displayed across the app</p></div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div>
                                <label for="siteLogo">Logo</label>
                                <input id="siteLogo" class="form-control @error('logo') is-invalid @enderror" type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg,image/*" data-image-preview="logoPreview">
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <span class="hint">JPG, PNG, WEBP or SVG, max 2 MB</span>
                                <div class="logo-box mt-2">
                                    <img id="logoPreview" src="{{ !empty($settings['logo_path']) ? asset('storage/'.$settings['logo_path']) : asset('paces/assets/images/logo.png') }}" alt="Logo preview">
                                    <div><strong>Logo preview</strong><small id="logoPreviewName">{{ !empty($settings['logo_path']) ? 'Current logo' : 'Paces default' }}</small></div>
                                </div>
                            </div>
                            <div>
                                <label for="siteFavicon">Favicon</label>
                                <input id="siteFavicon" class="form-control @error('favicon') is-invalid @enderror" type="file" name="favicon" accept=".ico,.jpg,.jpeg,.png,.webp,image/*" data-image-preview="faviconPreview">
                                @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <span class="hint">ICO, PNG, JPG or WEBP, max 512 KB</span>
                                <div class="logo-box mt-2">
                                    <img id="faviconPreview" src="{{ !empty($settings['favicon_path']) ? asset('storage/'.$settings['favicon_path']) : asset('paces/assets/images/favicon.ico') }}" alt="Favicon preview">
                                    <div><strong>Favicon preview</strong><small id="faviconPreviewName">{{ !empty($settings['favicon_path']) ? 'Current favicon' : 'Paces default' }}</small></div>
                                </div>
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
                                    @foreach(['smtp'=>'SMTP','log'=>'Log (Development)'] as $v => $l)
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
            <form class="s-card" method="POST" action="{{ route('settings.general.mail.test') }}">
                @csrf
                <div class="s-card-head"><div class="card-icon" style="background:#e8f5ef;color:#14836b"><i class="ti ti-send"></i></div><div><h3>Send test email</h3><p>Uses the saved SMTP settings</p></div></div>
                <div class="s-card-body">
                    @if(session('mail_test_success'))<div class="alert alert-success">{{ session('mail_test_success') }}</div>@endif
                    <label for="testEmail" class="form-label">Recipient email</label>
                    <div class="d-flex gap-2 flex-wrap">
                        <input id="testEmail" class="form-control @error('test_email') is-invalid @enderror" type="email" name="test_email" value="{{ old('test_email', auth()->user()->email) }}" required style="max-width:360px">
                        <button class="btn btn-outline-primary" type="submit"><i class="ti ti-send me-1"></i>Send Test</button>
                        @error('test_email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </form>
        </div>

        {{-- ════════════════════════════════════════════════════
             TAB 3 — ENVIRONMENT
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


/* Update branding previews before saving. */
document.querySelectorAll('[data-image-preview]').forEach(function(input) {
    var currentUrl;
    input.addEventListener('change', function() {
        var image = document.getElementById(input.dataset.imagePreview);
        var label = document.getElementById(input.dataset.imagePreview + 'Name');
        if (!image || !input.files || !input.files[0]) return;
        if (currentUrl) URL.revokeObjectURL(currentUrl);
        currentUrl = URL.createObjectURL(input.files[0]);
        image.src = currentUrl;
        if (label) label.textContent = input.files[0].name;
    });
});

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
