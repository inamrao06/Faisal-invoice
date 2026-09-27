@extends('layouts.app')
@section('title', 'Theme Settings')
@section('page_title', 'Theme Settings')

@section('content')
<div class="page-actions"><div><h2>Theme Settings</h2><p>Dashboard colors and display mode.</p></div></div>
<form class="theme-settings" method="POST" action="{{ route('settings.theme.update') }}">
    @csrf
    <div class="row g-3">
        <div class="col-xl-7">
            <section class="card theme-settings-card">
                <div class="card-header"><h3 class="card-title mb-0">Appearance</h3></div>
                <div class="card-body">
                    <label class="form-label d-block">Color mode</label>
                    <div class="theme-mode-control" role="group" aria-label="Color mode">
                        <label><input type="radio" name="theme_mode" value="light" @checked(old('theme_mode', $settings['theme_mode'] ?? 'light') === 'light')><span><i class="ti ti-sun me-1"></i>Light</span></label>
                        <label><input type="radio" name="theme_mode" value="dark" @checked(old('theme_mode', $settings['theme_mode'] ?? 'light') === 'dark')><span><i class="ti ti-moon me-1"></i>Dark</span></label>
                    </div>
                    @error('theme_mode')<div class="text-danger small mt-2">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="accentColor">Accent color</label><small>Buttons, links, and active navigation</small></div>
                        <input id="accentColor" type="color" name="accent_color" value="{{ old('accent_color', $settings['accent_color'] ?? '#236dc9') }}" aria-label="Accent color">
                    </div>
                    <div class="theme-swatches" data-color-target="accentColor" aria-label="Accent presets">
                        <button type="button" style="--swatch:#236dc9" data-color="#236dc9" aria-label="Paces blue" title="Paces blue"></button>
                        <button type="button" style="--swatch:#02bc9c" data-color="#02bc9c" aria-label="Paces green" title="Paces green"></button>
                        <button type="button" style="--swatch:#7b70ef" data-color="#7b70ef" aria-label="Paces violet" title="Paces violet"></button>
                        <button type="button" style="--swatch:#e45b5b" data-color="#e45b5b" aria-label="Coral" title="Coral"></button>
                    </div>
                    @error('accent_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="activeColor">Active color</label><small>Selected menu items, focused controls, and active tabs</small></div>
                        <input id="activeColor" type="color" name="active_color" value="{{ old('active_color', $settings['active_color'] ?? ($settings['accent_color'] ?? '#236dc9')) }}" aria-label="Active color">
                    </div>
                    <div class="theme-swatches" data-color-target="activeColor" aria-label="Active presets">
                        <button type="button" style="--swatch:#236dc9" data-color="#236dc9" aria-label="Blue" title="Blue"></button>
                        <button type="button" style="--swatch:#16a34a" data-color="#16a34a" aria-label="Green" title="Green"></button>
                        <button type="button" style="--swatch:#d97706" data-color="#d97706" aria-label="Amber" title="Amber"></button>
                        <button type="button" style="--swatch:#dc2626" data-color="#dc2626" aria-label="Red" title="Red"></button>
                    </div>
                    @error('active_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="borderColor">Border color</label><small>Cards, tables, inputs, and separators</small></div>
                        <input id="borderColor" type="color" name="border_color" value="{{ old('border_color', $settings['border_color'] ?? '#e5e7ef') }}" aria-label="Border color">
                    </div>
                    <div class="theme-swatches" data-color-target="borderColor" aria-label="Border presets">
                        <button type="button" style="--swatch:#e5e7ef" data-color="#e5e7ef" aria-label="Soft gray" title="Soft gray"></button>
                        <button type="button" style="--swatch:#cbd5e1" data-color="#cbd5e1" aria-label="Slate" title="Slate"></button>
                        <button type="button" style="--swatch:#d6bcfa" data-color="#d6bcfa" aria-label="Lavender" title="Lavender"></button>
                        <button type="button" style="--swatch:#94a3b8" data-color="#94a3b8" aria-label="Strong gray" title="Strong gray"></button>
                    </div>
                    @error('border_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="sidebarColor">Sidebar color</label><small>Navigation background</small></div>
                        <input id="sidebarColor" type="color" name="sidebar_color" value="{{ old('sidebar_color', $settings['sidebar_color'] ?? '#1e1f27') }}" aria-label="Sidebar color">
                    </div>
                    <div class="theme-swatches" data-color-target="sidebarColor" aria-label="Sidebar presets">
                        <button type="button" style="--swatch:#1e1f27" data-color="#1e1f27" aria-label="Paces dark" title="Paces dark"></button>
                        <button type="button" style="--swatch:#263a36" data-color="#263a36" aria-label="Deep green" title="Deep green"></button>
                        <button type="button" style="--swatch:#374151" data-color="#374151" aria-label="Gray" title="Gray"></button>
                        <button type="button" style="--swatch:#ffffff" data-color="#ffffff" aria-label="White" title="White"></button>
                    </div>
                    @error('sidebar_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="headerColor">Header color</label><small>Top navigation background</small></div>
                        <input id="headerColor" type="color" name="header_color" value="{{ old('header_color', $settings['header_color'] ?? '#ffffff') }}" aria-label="Header color">
                    </div>
                    <div class="theme-swatches" data-color-target="headerColor" aria-label="Header presets">
                        <button type="button" style="--swatch:#ffffff" data-color="#ffffff" aria-label="White" title="White"></button>
                        <button type="button" style="--swatch:#f6f7fb" data-color="#f6f7fb" aria-label="Paces gray" title="Paces gray"></button>
                        <button type="button" style="--swatch:#1e1f27" data-color="#1e1f27" aria-label="Paces dark" title="Paces dark"></button>
                    </div>
                    @error('header_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="bodyBgColor">Page background</label><small>Main dashboard canvas</small></div>
                        <input id="bodyBgColor" type="color" name="body_bg_color" value="{{ old('body_bg_color', $settings['body_bg_color'] ?? (($settings['theme_mode'] ?? 'light') === 'dark' ? '#252831' : '#f6f7fb')) }}" aria-label="Page background">
                    </div>
                    @error('body_bg_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="cardBgColor">Card background</label><small>Panels, widgets, and form cards</small></div>
                        <input id="cardBgColor" type="color" name="card_bg_color" value="{{ old('card_bg_color', $settings['card_bg_color'] ?? (($settings['theme_mode'] ?? 'light') === 'dark' ? '#2b2f39' : '#ffffff')) }}" aria-label="Card background">
                    </div>
                    @error('card_bg_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="textColor">Text color</label><small>Primary text throughout the system</small></div>
                        <input id="textColor" type="color" name="text_color" value="{{ old('text_color', $settings['text_color'] ?? (($settings['theme_mode'] ?? 'light') === 'dark' ? '#f0f3f6' : '#26313d')) }}" aria-label="Text color">
                    </div>
                    @error('text_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="mutedColor">Muted text color</label><small>Labels, help text, and secondary text</small></div>
                        <input id="mutedColor" type="color" name="muted_color" value="{{ old('muted_color', $settings['muted_color'] ?? (($settings['theme_mode'] ?? 'light') === 'dark' ? '#aab3c2' : '#8c98a9')) }}" aria-label="Muted text color">
                    </div>
                    @error('muted_color')<div class="text-danger small">{{ $message }}</div>@enderror

                    <div class="theme-color-row">
                        <div><label class="form-label" for="inputBgColor">Input background</label><small>Text fields, selects, and filter boxes</small></div>
                        <input id="inputBgColor" type="color" name="input_bg_color" value="{{ old('input_bg_color', $settings['input_bg_color'] ?? ($settings['card_bg_color'] ?? '#ffffff')) }}" aria-label="Input background">
                    </div>
                    @error('input_bg_color')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="card-footer d-flex justify-content-end"><button class="btn btn-primary" type="submit"><i class="ti ti-device-floppy me-1"></i>Save Theme</button></div>
            </section>
        </div>
        <div class="col-xl-5">
            <section class="card theme-settings-card">
                <div class="card-header"><h3 class="card-title mb-0">Preview</h3></div>
                <div class="card-body">
                    <div id="themePreview" class="theme-preview">
                        <div class="theme-preview-sidebar"><strong>{{ $settings['site_name'] ?? 'Paces' }}</strong><span>Dashboard</span><span>Companies</span><span>Settings</span></div>
                        <div class="theme-preview-main"><div class="theme-preview-topbar">Dashboard <i class="ti ti-user-circle"></i></div><div class="theme-preview-content"><b>Overview</b><div class="theme-preview-metrics"><span>Companies</span><span>Users</span></div><div class="theme-preview-button">View details</div></div></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/theme-settings.js') }}?v=2"></script>
@endpush
