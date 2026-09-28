@extends('layouts.app')
@section('title',$template->exists ? 'Edit Template' : 'Create Template')
@section('page_title',$template->exists ? 'Edit Template' : 'Create Template')
@push('styles')
<link rel="stylesheet" href="{{ asset('paces/assets/plugins/summernote/summernote-bs5.min.css') }}">
<style>
    .template-builder {
        align-items: flex-start;
    }
    .template-main-card,
    .template-side-card {
        border: 1px solid #dce6f2;
        box-shadow: 0 10px 28px rgba(18, 38, 63, .04);
    }
    .template-main-card .card-header,
    .template-side-card .card-header {
        background: #fff;
    }
    .template-editor-shell .note-editor.note-frame {
        border-color: #d6e3f3;
        border-radius: 8px;
        overflow: hidden;
    }
    .template-editor-shell .note-toolbar {
        background: #f6f9fd;
        border-bottom-color: #d6e3f3;
    }
    .template-editor-shell .note-editable {
        min-height: 390px;
        color: #152238;
        font-family: Arial, sans-serif;
        line-height: 1.55;
    }
    .template-sidebar {
        position: sticky;
        top: 88px;
    }
    .template-tag-search {
        border-color: #d6e3f3;
        border-radius: 8px;
        font-size: 13px;
    }
    .template-tag-tabs {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
    }
    .template-tag-tab {
        border: 1px solid #d6e3f3;
        border-radius: 8px;
        background: #fff;
        color: #516173;
        font-size: 12px;
        font-weight: 600;
        padding: 8px;
        text-align: center;
    }
    .template-tag-tab.active {
        background: #1f55c8;
        border-color: #1f55c8;
        color: #fff;
    }
    .template-tag-list {
        max-height: 560px;
        overflow: auto;
        padding-right: 4px;
    }
    .template-tag-group {
        display: none;
    }
    .template-tag-group.active {
        display: block;
    }
    .template-tag-button {
        border: 1px solid #cfe0f2;
        border-radius: 8px;
        background: #f8fbff;
        color: #14315c;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace;
        font-size: 12px;
        padding: 7px 9px;
        text-align: left;
        transition: all .15s ease;
        width: 100%;
    }
    .template-tag-button:hover {
        background: #eaf3ff;
        border-color: #8bb8ec;
        color: #0f4c9d;
    }
    .template-preview {
        background: #fff;
        border: 1px solid #dce6f2;
        border-radius: 10px;
        min-height: 180px;
        max-height: 360px;
        overflow: auto;
        padding: 18px;
    }
    .template-company-values {
        max-height: 290px;
        overflow: auto;
    }
    .template-empty-tags {
        display: none;
        border: 1px dashed #d6e3f3;
        border-radius: 8px;
        color: #728095;
        font-size: 13px;
        padding: 16px;
        text-align: center;
    }
    @media (max-width: 1199.98px) {
        .template-sidebar {
            position: static;
        }
        .template-tag-list {
            max-height: 380px;
        }
    }
</style>
@endpush
@section('content')
<div class="row g-3 template-builder">
    <div class="col-xl-8">
        <form class="card form-card template-main-card mb-0" method="POST" action="{{ $template->exists ? route('company.templates.update',$template) : route('company.templates.store') }}">
            @csrf
            @if($template->exists)@method('PUT')@endif
            <div class="card-header border-light">
                <div class="form-section-title mb-0">
                    <div class="fs-icon"><i class="ti ti-template"></i></div>
                    <div>
                        <h6>{{ $template->exists ? 'Edit Template' : 'Create Template' }}</h6>
                        <p>Use tags like [company_name], [invoice_no], and [expense_amount]. HTML is supported.</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">Template Name <span class="text-danger">*</span></label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name',$template->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="type">Template Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                            @foreach($types as $type)
                                <option value="{{ $type }}" @selected(old('type',$template->type)===$type)>{{ str($type)->replace('_',' ')->title() }}</option>
                            @endforeach
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="body">Template Body <span class="text-danger">*</span></label>
                        <div class="template-editor-shell">
                            <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="18" required>{{ old('body',$template->body ?: '<div class="invoice-sheet"><h2>[company_name]</h2><p>[company_phone] [company_email]</p><hr><h3>Invoice [invoice_no]</h3><p>Date: [invoice_date]</p><p>Customer: [buyer_name]</p><p>Total: [invoice_amount]</p><p>Balance: [balance_amount]</p></div>') }}</textarea>
                        </div>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label mb-0">Live Preview</label>
                            <button class="btn btn-sm btn-light" type="button" id="refresh-template-preview">
                                <i class="ti ti-refresh me-1"></i>Refresh
                            </button>
                        </div>
                        <div class="template-preview" id="template-preview"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_default" value="1" @checked(old('is_default',$template->is_default))>
                            <span class="form-check-label">Use as default for this type</span>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$template->exists ? $template->is_active : true))>
                            <span class="form-check-label">Active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn btn-primary" type="submit"><i class="ti ti-check me-1"></i>Save Template</button>
                <a class="btn btn-light" href="{{ route('company.templates.index') }}">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-xl-4">
        <div class="template-sidebar">
        <div class="card template-side-card mb-3">
            <div class="card-header border-light">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Insert Tags</h6>
                    <span class="badge bg-light text-muted">{{ collect($tags)->sum(fn($items) => count($items)) }}</span>
                </div>
            </div>
            <div class="card-body">
                <input class="form-control template-tag-search mb-3" id="template-tag-search" placeholder="Search tags..." type="search">
                <div class="template-tag-tabs mb-3" role="tablist">
                    @foreach($tags as $group => $items)
                        <button class="template-tag-tab @if($loop->first) active @endif" type="button" data-tag-tab="{{ $group }}">
                            {{ str($group)->replace('_',' ')->title() }}
                        </button>
                    @endforeach
                </div>
                <div class="template-tag-list" id="template-tag-list">
                @foreach($tags as $group => $items)
                    <div class="template-tag-group @if($loop->first) active @endif" data-tag-group="{{ $group }}">
                        <div class="d-grid gap-2">
                            @foreach($items as $tag => $description)
                                <button class="template-tag-button" type="button" data-tag="{{ $tag }}" data-search="{{ str($tag.' '.$description)->lower() }}" title="{{ $description }}">
                                    {{ $tag }}
                                    <small class="text-muted d-block">{{ $description }}</small>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                </div>
                <div class="template-empty-tags" id="template-empty-tags">No matching tags found.</div>
            </div>
        </div>
        <div class="card template-side-card">
            <div class="card-header border-light"><h6 class="mb-0">Company Values</h6></div>
            <div class="card-body d-grid gap-2 template-company-values">
                @foreach($companyTags as $tag => $value)
                    <div><code>{{ $tag }}</code><small class="text-muted d-block">{{ filled($value) ? $value : '-' }}</small></div>
                @endforeach
            </div>
        </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('paces/assets/plugins/summernote/summernote-bs5.min.js') }}"></script>
@php
    $sampleValues = array_merge($companyTags, [
        '[invoice_no]' => 'INV-000001',
        '[invoice_date]' => now()->format('Y-m-d'),
        '[sale_date]' => now()->format('Y-m-d'),
        '[sale_type]' => 'Customer to Customer',
        '[buyer_name]' => 'Demo Buyer Customer',
        '[buyer_phone]' => '+92 300 0000000',
        '[buyer_email]' => 'buyer@example.com',
        '[seller_name]' => 'Demo Seller Customer',
        '[seller_phone]' => '+92 311 0000000',
        '[vehicle]' => 'Toyota Aqua 2022',
        '[registration]' => 'CTC-TEST-1',
        '[vin]' => 'VIN-TEST-001',
        '[vehicle_price]' => '2,850,000.00',
        '[discount]' => '0.00',
        '[commission]' => '50,000.00',
        '[invoice_amount]' => '2,850,000.00',
        '[paid_amount]' => '500,000.00',
        '[balance_amount]' => '2,350,000.00',
        '[payment_status]' => 'Partial',
        '[expense_date]' => now()->format('Y-m-d'),
        '[expense_amount]' => '25,000.00',
        '[expense_category]' => 'Maintenance',
        '[expense_description]' => 'Vehicle preparation and service',
        '[payment]' => 'Bank Transfer',
        '[expense_lists]' => 'Oil change, detailing, inspection',
    ]);
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const preview = document.getElementById('template-preview');
        const textarea = document.getElementById('body');
        const typeSelect = document.getElementById('type');
        const searchInput = document.getElementById('template-tag-search');
        const emptyTags = document.getElementById('template-empty-tags');
        const sampleValues = @json($sampleValues);

        const getBody = () => window.jQuery && jQuery.fn.summernote
            ? jQuery(textarea).summernote('code')
            : textarea.value;

        const updatePreview = () => {
            let html = getBody();
            Object.entries(sampleValues).forEach(([tag, value]) => {
                html = html.split(tag).join(value || '-');
            });
            preview.innerHTML = html;
        };

        if (window.jQuery && jQuery.fn.summernote) {
            jQuery(textarea).summernote({
                height: 460,
                placeholder: 'Design your invoice or expense template...',
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'hr']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                callbacks: {
                    onChange: updatePreview,
                    onInit: updatePreview
                }
            });
        } else {
            updatePreview();
            textarea.addEventListener('input', updatePreview);
        }

        document.querySelectorAll('.template-tag-button').forEach((button) => {
            button.addEventListener('click', () => {
                const tag = button.dataset.tag;
                if (window.jQuery && jQuery.fn.summernote) {
                    jQuery(textarea).summernote('insertText', tag);
                } else {
                    const start = textarea.selectionStart || textarea.value.length;
                    const end = textarea.selectionEnd || textarea.value.length;
                    textarea.value = textarea.value.slice(0, start) + tag + textarea.value.slice(end);
                    textarea.focus();
                    textarea.setSelectionRange(start + tag.length, start + tag.length);
                }
                updatePreview();
            });
        });

        const activateTagGroup = (group) => {
            const targetGroup = document.querySelector(`[data-tag-group="${group}"]`) ? group : 'company';
            document.querySelectorAll('.template-tag-tab').forEach((tabButton) => {
                tabButton.classList.toggle('active', tabButton.dataset.tagTab === targetGroup);
            });
            document.querySelectorAll('.template-tag-group').forEach((tagGroup) => {
                tagGroup.classList.toggle('active', tagGroup.dataset.tagGroup === targetGroup);
            });
            filterTags();
        };

        const filterTags = () => {
            const query = (searchInput.value || '').trim().toLowerCase();
            let visibleCount = 0;
            document.querySelectorAll('.template-tag-group.active .template-tag-button').forEach((button) => {
                const isVisible = !query || button.dataset.search.includes(query);
                button.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });
            emptyTags.style.display = visibleCount ? 'none' : 'block';
        };

        document.querySelectorAll('.template-tag-tab').forEach((button) => {
            button.addEventListener('click', () => activateTagGroup(button.dataset.tagTab));
        });
        searchInput.addEventListener('input', filterTags);
        typeSelect.addEventListener('change', () => activateTagGroup(typeSelect.value));
        activateTagGroup(typeSelect.value);

        document.getElementById('refresh-template-preview').addEventListener('click', updatePreview);
        textarea.closest('form').addEventListener('submit', function () {
            if (window.jQuery && jQuery.fn.summernote) {
                jQuery(textarea).val(jQuery(textarea).summernote('code'));
            }
        });
    });
</script>
@endpush
