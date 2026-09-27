@extends('layouts.app')
@section('title', 'Theme Settings')
@section('page_title', 'Theme Settings')

@section('content')
@php
    $isDarkMode = ($settings['theme_mode'] ?? 'light') === 'dark';
    $colorGroups = [
        [
            'title' => 'Brand Colors',
            'hint'  => 'Primary identity used on buttons, links and active navigation',
            'icon'  => 'ti-palette',
            'items' => [
                ['name' => 'accent_color', 'id' => 'accentColor', 'label' => 'Accent color', 'hint' => 'Buttons, links and active navigation',
                 'value' => $settings['accent_color'] ?? '#236dc9',
                 'presets' => ['#236dc9' => 'Paces blue', '#02bc9c' => 'Paces green', '#7b70ef' => 'Paces violet', '#e45b5b' => 'Coral']],
                ['name' => 'active_color', 'id' => 'activeColor', 'label' => 'Active color', 'hint' => 'Selected menu items and focused controls',
                 'value' => $settings['active_color'] ?? ($settings['accent_color'] ?? '#236dc9'),
                 'presets' => ['#236dc9' => 'Blue', '#16a34a' => 'Green', '#d97706' => 'Amber', '#dc2626' => 'Red']],
                ['name' => 'border_color', 'id' => 'borderColor', 'label' => 'Border color', 'hint' => 'Cards, tables, inputs and separators',
                 'value' => $settings['border_color'] ?? '#e5e7ef',
                 'presets' => ['#e5e7ef' => 'Soft gray', '#cbd5e1' => 'Slate', '#d6bcfa' => 'Lavender', '#94a3b8' => 'Strong gray']],
            ],
        ],
        [
            'title' => 'Surfaces',
            'hint'  => 'Backgrounds for the navigation, topbar, page and cards',
            'icon'  => 'ti-layout-board-split',
            'items' => [
                ['name' => 'sidebar_color', 'id' => 'sidebarColor', 'label' => 'Sidebar color', 'hint' => 'Navigation background',
                 'value' => $settings['sidebar_color'] ?? '#1e1f27',
                 'presets' => ['#1e1f27' => 'Paces dark', '#263a36' => 'Deep green', '#374151' => 'Gray', '#ffffff' => 'White']],
                ['name' => 'header_color', 'id' => 'headerColor', 'label' => 'Header color', 'hint' => 'Top navigation background',
                 'value' => $settings['header_color'] ?? '#ffffff',
                 'presets' => ['#ffffff' => 'White', '#f6f7fb' => 'Paces gray', '#1e1f27' => 'Paces dark']],
                ['name' => 'body_bg_color', 'id' => 'bodyBgColor', 'label' => 'Page background', 'hint' => 'Main dashboard canvas',
                 'value' => $settings['body_bg_color'] ?? ($isDarkMode ? '#252831' : '#f6f7fb'),
                 'presets' => ['#f6f7fb' => 'Light gray', '#ffffff' => 'White', '#252831' => 'Dark', '#eef2f7' => 'Cool gray']],
                ['name' => 'card_bg_color', 'id' => 'cardBgColor', 'label' => 'Card background', 'hint' => 'Panels, widgets and form cards',
                 'value' => $settings['card_bg_color'] ?? ($isDarkMode ? '#2b2f39' : '#ffffff'),
                 'presets' => ['#ffffff' => 'White', '#2b2f39' => 'Dark', '#f8fafc' => 'Soft white', '#313640' => 'Slate dark']],
                ['name' => 'input_bg_color', 'id' => 'inputBgColor', 'label' => 'Input background', 'hint' => 'Text fields, selects and filters',
                 'value' => $settings['input_bg_color'] ?? ($settings['card_bg_color'] ?? '#ffffff'),
                 'presets' => ['#ffffff' => 'White', '#f8fafc' => 'Soft white', '#2b2f39' => 'Dark']],
            ],
        ],
        [
            'title' => 'Text',
            'hint'  => 'Reading colors for primary and secondary content',
            'icon'  => 'ti-typography',
            'items' => [
                ['name' => 'text_color', 'id' => 'textColor', 'label' => 'Text color', 'hint' => 'Primary text throughout the system',
                 'value' => $settings['text_color'] ?? ($isDarkMode ? '#f0f3f6' : '#26313d'),
                 'presets' => ['#26313d' => 'Ink', '#0f172a' => 'Deep ink', '#f0f3f6' => 'Light', '#334155' => 'Slate']],
                ['name' => 'muted_color', 'id' => 'mutedColor', 'label' => 'Muted text color', 'hint' => 'Labels, help text and secondary text',
                 'value' => $settings['muted_color'] ?? ($isDarkMode ? '#aab3c2' : '#8c98a9'),
                 'presets' => ['#8c98a9' => 'Gray', '#94a3b8' => 'Slate', '#aab3c2' => 'Light gray', '#64748b' => 'Dark gray']],
            ],
        ],
    ];
@endphp

<form class="theme-settings" method="POST" action="{{ route('settings.theme.update') }}">
    @csrf
    <div class="row g-3">
        <div class="col-xxl-8 col-xl-7">
            <div class="card mb-0">
                <div class="card-header border-light">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-brush"></i></div>
                        <div>
                            <h6>Appearance</h6>
                            <p>Pick a mode, then fine-tune every color. Hex codes update as you choose.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap pb-1 mb-4 border-bottom">
                        <div>
                            <label class="form-label mb-1 d-block">Color mode</label>
                            <p class="text-muted fs-12 mb-0">Dark mode dims every surface for low-light use.</p>
                        </div>
                        <div class="theme-mode-control" role="group" aria-label="Color mode">
                            <label>
                                <input type="radio" name="theme_mode" value="light"
                                       @checked(old('theme_mode', $settings['theme_mode'] ?? 'light') === 'light')>
                                <span><i class="ti ti-sun me-1"></i>Light</span>
                            </label>
                            <label>
                                <input type="radio" name="theme_mode" value="dark"
                                       @checked(old('theme_mode', $settings['theme_mode'] ?? 'light') === 'dark')>
                                <span><i class="ti ti-moon me-1"></i>Dark</span>
                            </label>
                        </div>
                    </div>
                    @error('theme_mode')<div class="text-danger small mb-3">{{ $message }}</div>@enderror

                    @foreach($colorGroups as $group)
                        <div class="form-section-title">
                            <div class="fs-icon"><i class="ti {{ $group['icon'] }}"></i></div>
                            <div>
                                <h6>{{ $group['title'] }}</h6>
                                <p>{{ $group['hint'] }}</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-2">
                            @foreach($group['items'] as $color)
                                @php $current = strtoupper(old($color['name'], $color['value'])); @endphp
                                <div class="col-md-6">
                                    <div class="color-card">
                                        <div class="color-card-top">
                                            <div>
                                                <label class="form-label mb-1" for="{{ $color['id'] }}">{{ $color['label'] }}</label>
                                                <p class="text-muted fs-12 mb-0">{{ $color['hint'] }}</p>
                                            </div>
                                            <input id="{{ $color['id'] }}" type="color" name="{{ $color['name'] }}"
                                                   value="{{ old($color['name'], $color['value']) }}"
                                                   aria-label="{{ $color['label'] }}">
                                        </div>
                                        <div class="color-card-foot">
                                            <code class="theme-hex" data-hex-for="{{ $color['id'] }}">{{ $current }}</code>
                                            @if(!empty($color['presets']))
                                                <div class="theme-swatches" data-color-target="{{ $color['id'] }}"
                                                     aria-label="{{ $color['label'] }} presets">
                                                    @foreach($color['presets'] as $hex => $presetTitle)
                                                        <button type="button" style="--swatch:{{ $hex }}" data-color="{{ $hex }}"
                                                                aria-label="{{ $presetTitle }}" title="{{ $presetTitle }}"></button>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        @error($color['name'])<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(!$loop->last)<hr class="my-4">@endif
                    @endforeach
                </div>

                <div class="card-footer d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <span class="text-muted fs-12">
                        <i class="ti ti-info-circle me-1"></i>Applies to every page after saving.
                    </span>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light" href="{{ route('settings.general') }}">Cancel</a>
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-device-floppy me-1"></i>Save Theme
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-xl-5">
            <div class="card mb-0" style="position:sticky;top:90px">
                <div class="card-header border-light">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-eye"></i></div>
                        <div>
                            <h6>Live Preview</h6>
                            <p>Updates as you pick colors</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="themePreview" class="theme-preview">
                        <div class="theme-preview-sidebar">
                            <strong>{{ $settings['site_name'] ?? 'Paces' }}</strong>
                            <span>Dashboard</span>
                            <span>Companies</span>
                            <span>Settings</span>
                        </div>
                        <div class="theme-preview-main">
                            <div class="theme-preview-topbar">Dashboard <i class="ti ti-user-circle"></i></div>
                            <div class="theme-preview-content">
                                <b>Overview</b>
                                <div class="theme-preview-metrics"><span>Companies</span><span>Users</span></div>
                                <div class="theme-preview-button">View details</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <p class="text-muted fs-12 text-uppercase fw-bold mb-2">Current palette</p>
                        <div class="d-flex flex-wrap gap-2" id="paletteSummary">
                            @foreach($colorGroups as $group)
                                @foreach($group['items'] as $color)
                                    <span class="theme-hex" data-hex-for="{{ $color['id'] }}"
                                          title="{{ $color['label'] }}">{{ strtoupper($color['value']) }}</span>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/theme-settings.js') }}?v=2"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-hex-for]').forEach((chip) => {
            const input = document.getElementById(chip.dataset.hexFor);
            if (!input) return;
            const sync = () => {
                chip.textContent = input.value.toUpperCase();
                chip.style.setProperty('--hex-bg', input.value);
            };
            input.addEventListener('input', sync);
            sync();
        });
    });
</script>
@endpush
