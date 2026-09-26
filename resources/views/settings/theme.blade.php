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
<script src="{{ asset('js/theme-settings.js') }}?v=1"></script>
@endpush
