@extends('layouts.app')
@section('title', 'Theme Settings')
@section('page_title', 'Theme Settings')

@section('content')
@php
    $isDarkMode = ($settings['theme_mode'] ?? 'light') === 'dark';
    $lightDefaults = [
        'accent_color'   => '#1D4ED8',
        'active_color'   => '#0F766E',
        'border_color'   => '#D8E0EA',
        'sidebar_color'  => '#111827',
        'header_color'   => '#FFFFFF',
        'body_bg_color'  => '#F3F7FB',
        'card_bg_color'  => '#FFFFFF',
        'input_bg_color' => '#F8FAFC',
        'text_color'     => '#18212F',
        'muted_color'    => '#667085',
    ];
    $darkDefaults = [
        'accent_color'   => '#3b82f6',
        'active_color'   => '#60a5fa',
        'border_color'   => '#334155',
        'sidebar_color'  => '#020617',
        'header_color'   => '#0f172a',
        'body_bg_color'  => '#0f172a',
        'card_bg_color'  => '#1e293b',
        'input_bg_color' => '#1e293b',
        'text_color'     => '#f1f5f9',
        'muted_color'    => '#94a3b8',
    ];
    $defaults = $isDarkMode ? $darkDefaults : $lightDefaults;

    $colorGroups = [
        [
            'title' => 'Brand Colors',
            'hint'  => 'Primary identity used on buttons, links and active navigation',
            'icon'  => 'ti-palette',
            'items' => [
                ['name' => 'accent_color', 'id' => 'accentColor', 'label' => 'Accent color', 'hint' => 'Buttons, links and active navigation',
                 'value' => $settings['accent_color'] ?? $defaults['accent_color'],
                 'presets' => $isDarkMode
                     ? ['#3b82f6' => 'Blue', '#60a5fa' => 'Light blue', '#818cf8' => 'Indigo', '#22d3ee' => 'Cyan']
                     : ['#1D4ED8' => 'Electric blue', '#0EA5E9' => 'Sky blue', '#6366F1' => 'Indigo', '#0F766E' => 'Teal']],
                ['name' => 'active_color', 'id' => 'activeColor', 'label' => 'Active color', 'hint' => 'Selected menu items and focused controls',
                 'value' => $settings['active_color'] ?? $defaults['active_color'],
                 'presets' => $isDarkMode
                     ? ['#60a5fa' => 'Light blue', '#3b82f6' => 'Blue', '#a78bfa' => 'Violet', '#2dd4bf' => 'Teal']
                     : ['#0F766E' => 'Deep teal', '#1D4ED8' => 'Electric blue', '#7C3AED' => 'Violet', '#0891B2' => 'Cyan']],
                ['name' => 'border_color', 'id' => 'borderColor', 'label' => 'Border color', 'hint' => 'Cards, tables, inputs and separators',
                 'value' => $settings['border_color'] ?? $defaults['border_color'],
                 'presets' => $isDarkMode
                     ? ['#334155' => 'Slate', '#475569' => 'Gray', '#1e293b' => 'Dark', '#3f3f46' => 'Zinc']
                     : ['#D8E0EA' => 'Soft steel', '#CBD5E1' => 'Slate', '#E5E7EB' => 'Gray', '#BAC7D5' => 'Strong steel']],
            ],
        ],
        [
            'title' => 'Surfaces',
            'hint'  => 'Backgrounds for the navigation, topbar, page and cards',
            'icon'  => 'ti-layout-board-split',
            'items' => [
                ['name' => 'sidebar_color', 'id' => 'sidebarColor', 'label' => 'Sidebar color', 'hint' => 'Navigation background',
                 'value' => $settings['sidebar_color'] ?? $defaults['sidebar_color'],
                 'presets' => $isDarkMode
                     ? ['#020617' => 'Black', '#0f172a' => 'Navy', '#1e293b' => 'Slate', '#18181b' => 'Zinc']
                     : ['#111827' => 'Graphite', '#0F172A' => 'Navy', '#1E3A5F' => 'Deep blue', '#334155' => 'Slate']],
                ['name' => 'header_color', 'id' => 'headerColor', 'label' => 'Header color', 'hint' => 'Top navigation background',
                 'value' => $settings['header_color'] ?? $defaults['header_color'],
                 'presets' => $isDarkMode
                     ? ['#0f172a' => 'Navy', '#1e293b' => 'Slate', '#020617' => 'Black']
                     : ['#ffffff' => 'White', '#f8fafc' => 'Off-white', '#f1f5f9' => 'Light gray']],
                ['name' => 'body_bg_color', 'id' => 'bodyBgColor', 'label' => 'Page background', 'hint' => 'Main dashboard canvas',
                 'value' => $settings['body_bg_color'] ?? $defaults['body_bg_color'],
                 'presets' => $isDarkMode
                     ? ['#0f172a' => 'Navy', '#1e293b' => 'Slate', '#020617' => 'Black', '#18181b' => 'Zinc']
                     : ['#F3F7FB' => 'Cool mist', '#F8FAFC' => 'Off-white', '#F1F5F9' => 'Light gray', '#EEF2F7' => 'Cool gray']],
                ['name' => 'card_bg_color', 'id' => 'cardBgColor', 'label' => 'Card background', 'hint' => 'Panels, widgets and form cards',
                 'value' => $settings['card_bg_color'] ?? $defaults['card_bg_color'],
                 'presets' => $isDarkMode
                     ? ['#1e293b' => 'Slate', '#334155' => 'Gray', '#0f172a' => 'Navy', '#27272a' => 'Zinc']
                     : ['#ffffff' => 'White', '#f8fafc' => 'Off-white', '#f1f5f9' => 'Light gray', '#f9fafb' => 'Soft white']],
                ['name' => 'input_bg_color', 'id' => 'inputBgColor', 'label' => 'Input background', 'hint' => 'Text fields, selects and filters',
                 'value' => $settings['input_bg_color'] ?? $defaults['input_bg_color'],
                 'presets' => $isDarkMode
                     ? ['#1e293b' => 'Slate', '#334155' => 'Gray', '#0f172a' => 'Navy']
                     : ['#F8FAFC' => 'Off-white', '#FFFFFF' => 'White', '#F1F5F9' => 'Light gray']],
            ],
        ],
        [
            'title' => 'Text',
            'hint'  => 'Reading colors for primary and secondary content',
            'icon'  => 'ti-typography',
            'items' => [
                ['name' => 'text_color', 'id' => 'textColor', 'label' => 'Text color', 'hint' => 'Primary text throughout the system',
                 'value' => $settings['text_color'] ?? $defaults['text_color'],
                 'presets' => $isDarkMode
                     ? ['#f1f5f9' => 'Light', '#e2e8f0' => 'Slate', '#f8fafc' => 'White', '#cbd5e1' => 'Gray']
                     : ['#18212F' => 'Ink', '#0F172A' => 'Navy', '#334155' => 'Dark slate', '#475569' => 'Gray']],
                ['name' => 'muted_color', 'id' => 'mutedColor', 'label' => 'Muted text color', 'hint' => 'Labels, help text and secondary text',
                 'value' => $settings['muted_color'] ?? $defaults['muted_color'],
                 'presets' => $isDarkMode
                     ? ['#94a3b8' => 'Slate', '#64748b' => 'Gray', '#cbd5e1' => 'Light gray', '#475569' => 'Dark gray']
                     : ['#667085' => 'Cool gray', '#64748B' => 'Slate', '#6B7280' => 'Dark gray', '#4B5563' => 'Charcoal']],
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
                                            <div class="color-picker-wrap">
                                                <input id="{{ $color['id'] }}" type="color" name="{{ $color['name'] }}"
                                                       value="{{ old($color['name'], $color['value']) }}"
                                                       aria-label="{{ $color['label'] }}">
                                                <input type="text" class="form-control color-hex-input"
                                                       id="{{ $color['id'] }}_hex"
                                                       value="{{ strtoupper(old($color['name'], $color['value'])) }}"
                                                       maxlength="7"
                                                       placeholder="#000000"
                                                       aria-label="{{ $color['label'] }} hex value"
                                                       data-hex-input="{{ $color['id'] }}"
                                                       style="width:110px;padding:6px 10px;font-size:12px;font-family:monospace;">
                                            </div>
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
<script src="{{ asset('js/theme-settings.js') }}?v=3"></script>
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

        /* Sync hex text input with color picker */
        document.querySelectorAll('[data-hex-input]').forEach((hexInput) => {
            const colorInput = document.getElementById(hexInput.dataset.hexInput);
            if (!colorInput) return;

            // When user types a valid hex code, update the color picker
            hexInput.addEventListener('input', () => {
                let val = hexInput.value.trim();
                if (!val.startsWith('#')) val = '#' + val;
                if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                    colorInput.value = val;
                    colorInput.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });

            // When user picks a color, update the text input
            colorInput.addEventListener('input', () => {
                hexInput.value = colorInput.value.toUpperCase();
            });
        });
    });
</script>
@endpush
