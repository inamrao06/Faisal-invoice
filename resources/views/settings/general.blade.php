@extends('layouts.app')
@section('title','General Settings')
@section('page_title','General Settings')
@section('content')

@if(session('success'))
<div class="alert alert-success"><i class="ti ti-circle-check me-1"></i>{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-danger"><i class="ti ti-alert-triangle me-1"></i>{{ $errors->first() }}</div>
@endif

@php $envBadge = $env['APP_ENV'] ?? 'local'; @endphp

<div class="row">
    {{-- ── LEFT NAV ── --}}
    <div class="col-xl-3">
        <div class="card mb-3">
            <div class="card-header border-light">
                <h5 class="mb-0">Settings</h5>
            </div>
            <div class="card-body p-2">
                <div class="nav flex-column settings-nav" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-app" type="button" role="tab">
                        <i class="ti ti-info-circle"></i> App Info
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-mail" type="button" role="tab">
                        <i class="ti ti-mail"></i> Mail / SMTP
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-env" type="button" role="tab">
                        <i class="ti ti-terminal"></i> Environment
                        <span class="badge bg-secondary bg-opacity-10 text-body ms-auto">{{ $envBadge }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT PANELS ── --}}
    <div class="col-xl-9">
        <div class="tab-content">

            {{-- ════════ TAB 1 — APP INFO ════════ --}}
            <div class="tab-pane fade show active" id="tab-app" role="tabpanel">
                <form method="POST" enctype="multipart/form-data" action="{{ route('settings.general.update') }}">
                    @csrf

                    <div class="card form-card">
                        <div class="card-header border-light">
                            <div class="form-section-title mb-0">
                                <div class="fs-icon"><i class="ti ti-building"></i></div>
                                <div>
                                    <h6>Business Identity</h6>
                                    <p>Site name, slug and contact information</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="site_name">Site / Business Name <span class="text-danger">*</span></label>
                                    <input class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name"
                                           value="{{ old('site_name', $settings['site_name'] ?? $settings['business_name'] ?? '') }}"
                                           placeholder="e.g. Head Office Motors" required>
                                    @error('site_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">Shown in browser title, sidebar and emails</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="slug">Slug / Short Code <span class="text-danger">*</span></label>
                                    <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                                           value="{{ old('slug', $settings['slug'] ?? '') }}"
                                           placeholder="e.g. head-office-motors" required>
                                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="number">Contact Number</label>
                                    <input class="form-control" id="number" name="number"
                                           value="{{ old('number', $settings['number'] ?? '') }}" placeholder="+92 300 0000000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Contact Email</label>
                                    <input class="form-control" id="email" type="email" name="email"
                                           value="{{ old('email', $settings['email'] ?? '') }}" placeholder="info@example.com">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="address">Business Address</label>
                                    <input class="form-control" id="address" name="address"
                                           value="{{ old('address', $settings['address'] ?? '') }}" placeholder="Street, City, Country">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="tagline">Tagline / Description</label>
                                    <input class="form-control" id="tagline" name="tagline"
                                           value="{{ old('tagline', $settings['tagline'] ?? '') }}" placeholder="Short tagline shown under site name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card form-card">
                        <div class="card-header border-light">
                            <div class="form-section-title mb-0">
                                <div class="fs-icon"><i class="ti ti-photo"></i></div>
                                <div>
                                    <h6>Branding</h6>
                                    <p>Logo and favicon displayed across the app</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="siteLogo">Logo</label>
                                    <input id="siteLogo" class="form-control @error('logo') is-invalid @enderror" type="file" name="logo"
                                           accept=".jpg,.jpeg,.png,.webp,.svg,image/*" data-image-preview="logoPreview">
                                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">JPG, PNG, WEBP or SVG, max 2 MB</div>
                                    <div class="d-flex align-items-center gap-3 mt-2">
                                        <div class="logo-tile">
                                            <img id="logoPreview" src="{{ !empty($settings['logo_path']) ? asset('storage/'.$settings['logo_path']) : asset('paces/assets/images/logo.png') }}" alt="Logo preview">
                                        </div>
                                        <div>
                                            <h6 class="cell-title mb-0">Logo preview</h6>
                                            <p class="cell-sub" id="logoPreviewName">{{ !empty($settings['logo_path']) ? 'Current logo' : 'Paces default' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="siteFavicon">Favicon</label>
                                    <input id="siteFavicon" class="form-control @error('favicon') is-invalid @enderror" type="file" name="favicon"
                                           accept=".ico,.jpg,.jpeg,.png,.webp,image/*" data-image-preview="faviconPreview">
                                    @error('favicon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <div class="form-text">ICO, PNG, JPG or WEBP, max 512 KB</div>
                                    <div class="d-flex align-items-center gap-3 mt-2">
                                        <div class="logo-tile">
                                            <img id="faviconPreview" src="{{ !empty($settings['favicon_path']) ? asset('storage/'.$settings['favicon_path']) : asset('paces/assets/images/favicon.ico') }}" alt="Favicon preview">
                                        </div>
                                        <div>
                                            <h6 class="cell-title mb-0">Favicon preview</h6>
                                            <p class="cell-sub" id="faviconPreviewName">{{ !empty($settings['favicon_path']) ? 'Current favicon' : 'Paces default' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card form-card mb-0">
                        <div class="card-header border-light">
                            <div class="form-section-title mb-0">
                                <div class="fs-icon"><i class="ti ti-world"></i></div>
                                <div>
                                    <h6>Locale &amp; Formats</h6>
                                    <p>Timezone, date format and language preferences</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="timezone">Timezone</label>
                                    <input class="form-control" id="timezone" name="timezone"
                                           value="{{ old('timezone', $settings['timezone'] ?? $env['APP_TIMEZONE'] ?? 'UTC') }}" placeholder="Asia/Karachi">
                                    <div class="form-text">PHP timezone identifier</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="date_format">Date Format</label>
                                    <select class="form-select" id="date_format" name="date_format" data-toggle="select2" data-placeholder="Select date format">
                                        @foreach(['d/m/Y'=>'DD/MM/YYYY', 'm/d/Y'=>'MM/DD/YYYY', 'Y-m-d'=>'YYYY-MM-DD', 'd M Y'=>'DD Mon YYYY'] as $fmt => $label)
                                        <option value="{{ $fmt }}" @selected(($settings['date_format'] ?? 'd/m/Y') === $fmt)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center gap-2 flex-wrap">
                            <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save App Info</button>
                            <span class="text-muted fs-12">Changes also update APP_NAME in .env</span>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ════════ TAB 2 — MAIL ════════ --}}
            <div class="tab-pane fade" id="tab-mail" role="tabpanel">
                <form method="POST" action="{{ route('settings.general.mail') }}">
                    @csrf
                    <div class="card form-card">
                        <div class="card-header border-light">
                            <div class="form-section-title mb-0">
                                <div class="fs-icon"><i class="ti ti-mail"></i></div>
                                <div>
                                    <h6>Mail / SMTP Configuration</h6>
                                    <p>Settings are written directly to your <code>.env</code> file</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="mailMailer">Mail Driver</label>
                                    <select class="form-select" id="mailMailer" name="mail_mailer" data-toggle="select2" data-placeholder="Select mail driver">
                                        @foreach(['smtp'=>'SMTP','log'=>'Log (Development)'] as $v => $l)
                                        <option value="{{ $v }}" @selected(($settings['mail_mailer'] ?? $env['MAIL_MAILER'] ?? 'log') === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12" id="smtpFields">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="mail_host">SMTP Host</label>
                                            <input class="form-control" id="mail_host" name="mail_host"
                                                   value="{{ old('mail_host', $settings['mail_host'] ?? $env['MAIL_HOST'] ?? '') }}" placeholder="smtp.mailtrap.io">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="mail_port">SMTP Port</label>
                                            <input class="form-control" id="mail_port" type="number" name="mail_port"
                                                   value="{{ old('mail_port', $settings['mail_port'] ?? $env['MAIL_PORT'] ?? '587') }}" placeholder="587">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="mail_username">Username</label>
                                            <input class="form-control" id="mail_username" name="mail_username"
                                                   value="{{ old('mail_username', $settings['mail_username'] ?? $env['MAIL_USERNAME'] ?? '') }}" placeholder="SMTP username">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="mail_password">Password</label>
                                            <input class="form-control" id="mail_password" type="password" name="mail_password"
                                                   value="{{ old('mail_password', $settings['mail_password'] ?? $env['MAIL_PASSWORD'] ?? '') }}"
                                                   placeholder="SMTP password" autocomplete="new-password">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="mail_encryption">Encryption</label>
                                            <select class="form-select" id="mail_encryption" name="mail_encryption" data-toggle="select2" data-placeholder="Select encryption">
                                                @foreach(['' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'] as $v => $l)
                                                <option value="{{ $v }}" @selected(($settings['mail_encryption'] ?? $env['MAIL_ENCRYPTION'] ?? 'tls') === $v)>{{ $l }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12"><hr class="my-1"></div>

                                <div class="col-md-6">
                                    <label class="form-label" for="mail_from_address">From Address</label>
                                    <input class="form-control" id="mail_from_address" type="email" name="mail_from_address"
                                           value="{{ old('mail_from_address', $settings['mail_from_address'] ?? $env['MAIL_FROM_ADDRESS'] ?? '') }}" placeholder="noreply@example.com">
                                    <div class="form-text">The "from" address in outgoing emails</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="mail_from_name">From Name</label>
                                    <input class="form-control" id="mail_from_name" name="mail_from_name"
                                           value="{{ old('mail_from_name', $settings['mail_from_name'] ?? $env['MAIL_FROM_NAME'] ?? '') }}" placeholder="BookingPro">
                                </div>
                            </div>

                            <div class="rounded border bg-light bg-opacity-50 p-3 mt-3">
                                <div class="text-uppercase fw-bold fs-12 text-muted mb-2">Current .env values</div>
                                <div class="row g-2">
                                    @foreach(['MAIL_MAILER','MAIL_HOST','MAIL_PORT','MAIL_FROM_ADDRESS'] as $k)
                                    <div class="col-md-6">
                                        <span class="fs-12 text-muted font-monospace">{{ $k }}</span>
                                        <span class="fs-12 text-muted">=</span>
                                        <strong class="fs-12">{{ $env[$k] ?? '—' }}</strong>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center gap-2 flex-wrap">
                            <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save Mail Settings</button>
                            <span class="text-muted fs-12">Writes to .env and clears config cache</span>
                        </div>
                    </div>
                </form>

                <form method="POST" action="{{ route('settings.general.mail.test') }}">
                    @csrf
                    <div class="card form-card mb-0">
                        <div class="card-header border-light">
                            <div class="form-section-title mb-0">
                                <div class="fs-icon"><i class="ti ti-send"></i></div>
                                <div>
                                    <h6>Send test email</h6>
                                    <p>Uses the saved SMTP settings</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('mail_test_success'))<div class="alert alert-success">{{ session('mail_test_success') }}</div>@endif
                            <label class="form-label" for="testEmail">Recipient email</label>
                            <div class="d-flex gap-2 flex-wrap align-items-start">
                                <div style="max-width:360px;flex:1 1 260px">
                                    <input id="testEmail" class="form-control @error('test_email') is-invalid @enderror" type="email" name="test_email"
                                           value="{{ old('test_email', auth()->user()->email) }}" required>
                                    @error('test_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <button class="btn btn-soft-primary" type="submit"><i class="ti ti-send me-1"></i>Send Test</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ════════ TAB 3 — ENVIRONMENT ════════ --}}
            <div class="tab-pane fade" id="tab-env" role="tabpanel">
                <form method="POST" action="{{ route('settings.general.env') }}">
                    @csrf
                    <div class="card form-card mb-0">
                        <div class="card-header border-light">
                            <div class="form-section-title mb-0">
                                <div class="fs-icon"><i class="ti ti-terminal"></i></div>
                                <div>
                                    <h6>Application Environment</h6>
                                    <p>Core .env values — changes clear config cache automatically</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label" for="app_url">App URL <span class="text-danger">*</span></label>
                                    <input class="form-control font-monospace" id="app_url" name="app_url" type="url"
                                           value="{{ old('app_url', $env['APP_URL'] ?? config('app.url')) }}" placeholder="https://example.com" required>
                                    <div class="form-text">Full URL with scheme — no trailing slash</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="appEnvSelect">Environment</label>
                                    <select class="form-select" id="appEnvSelect" name="app_env" data-toggle="select2" data-placeholder="Select environment">
                                        @foreach(['local'=>'Local (Development)','staging'=>'Staging','production'=>'Production'] as $v => $l)
                                        <option value="{{ $v }}" @selected(($env['APP_ENV'] ?? 'local') === $v)>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="app_locale">App Locale</label>
                                    <input class="form-control" id="app_locale" name="app_locale"
                                           value="{{ old('app_locale', $env['APP_LOCALE'] ?? 'en') }}" placeholder="en">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="app_timezone">Timezone</label>
                                    <input class="form-control" id="app_timezone" name="app_timezone"
                                           value="{{ old('app_timezone', $env['APP_TIMEZONE'] ?? 'UTC') }}" placeholder="Asia/Karachi">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="app_debug" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="debugToggle" name="app_debug" value="1"
                                               {{ ($env['APP_DEBUG'] ?? 'false') === 'true' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="debugToggle">
                                            Debug Mode
                                            <span class="d-block fs-12 text-muted fw-normal">Show detailed error messages</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="env-snapshot mt-4">
                                <div class="env-title">.env snapshot</div>
                                @foreach(['APP_NAME','APP_ENV','APP_URL','APP_DEBUG','APP_TIMEZONE','APP_LOCALE',
                                          'DB_CONNECTION','MAIL_MAILER','QUEUE_CONNECTION','FILESYSTEM_DISK'] as $k)
                                    @if(isset($env[$k]))
                                    <div class="env-row">
                                        <span class="env-key">{{ $k }}</span>
                                        <span class="env-eq">=</span>
                                        <span class="env-val">{{ Str::limit($env[$k], 60) }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>

                            <div class="alert alert-danger mt-3 d-none" id="prodWarning" role="alert">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Production mode:</strong> make sure <code>APP_DEBUG</code> is <strong>false</strong> and all secrets are properly set before deploying.
                            </div>
                        </div>
                        <div class="card-footer d-flex align-items-center gap-2 flex-wrap">
                            <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save Environment</button>
                            <span class="text-muted fs-12">Runs <code>php artisan config:clear</code> after save</span>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* Restore tab from URL hash and keep the hash in sync. */
(function () {
    var pills = document.querySelectorAll('.settings-nav [data-bs-toggle="pill"]');
    var activate = function (id) {
        var target = document.getElementById(id);
        var trigger = document.querySelector('.settings-nav [data-bs-target="#' + id + '"]');
        if (!target || !trigger || !window.bootstrap) return;
        bootstrap.Tab.getOrCreateInstance(trigger).show();
    };

    var hash = location.hash.replace('#', '');
    if (hash && document.getElementById(hash)) activate(hash);

    pills.forEach(function (pill) {
        pill.addEventListener('shown.bs.tab', function () {
            history.replaceState(null, '', pill.dataset.bsTarget);
            if (window.jQuery && jQuery.fn.select2) jQuery(window).trigger('resize');
        });
    });
    window.addEventListener('hashchange', function () {
        var id = location.hash.replace('#', '');
        if (id && document.getElementById(id)) activate(id);
    });
})();

/* Update branding previews before saving. */
document.querySelectorAll('[data-image-preview]').forEach(function (input) {
    var currentUrl;
    input.addEventListener('change', function () {
        var image = document.getElementById(input.dataset.imagePreview);
        var label = document.getElementById(input.dataset.imagePreview + 'Name');
        if (!image || !input.files || !input.files[0]) return;
        if (currentUrl) URL.revokeObjectURL(currentUrl);
        currentUrl = URL.createObjectURL(input.files[0]);
        image.src = currentUrl;
        if (label) label.textContent = input.files[0].name;
    });
});

/* ENV: production warning */
var envSel = document.getElementById('appEnvSelect');
function checkEnv() {
    var warn = document.getElementById('prodWarning');
    if (warn) warn.classList.toggle('d-none', !(envSel && envSel.value === 'production'));
}
if (envSel) { envSel.addEventListener('change', checkEnv); checkEnv(); }

/* Mail: dim SMTP fields when not using SMTP */
var mailSel = document.getElementById('mailMailer');
function checkMailer() {
    var smtp = document.getElementById('smtpFields');
    if (!smtp) return;
    var show = mailSel && ['smtp', 'sendmail'].includes(mailSel.value);
    smtp.style.opacity = show ? '1' : '.4';
}
if (mailSel) { mailSel.addEventListener('change', checkMailer); checkMailer(); }
</script>
@endpush
