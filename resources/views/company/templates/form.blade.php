@extends('layouts.app')
@section('title',$template->exists ? 'Edit Template' : 'Create Template')
@section('page_title',$template->exists ? 'Edit Template' : 'Create Template')
@push('styles')
<link rel="stylesheet" href="{{ asset('paces/assets/plugins/summernote/summernote-bs5.min.css') }}">
<style>
    .template-builder{align-items:flex-start}
    .template-main-card,.template-side-card{border-color:var(--bs-border-color);background:var(--bs-card-bg)}
    .template-editor-shell .note-editor.note-frame{overflow:hidden;border-color:var(--bs-border-color);border-radius:8px;background:var(--bs-body-bg)}
    .template-editor-shell .note-toolbar{border-bottom-color:var(--bs-border-color);background:var(--bs-tertiary-bg)}
    .template-editor-shell .note-editable{min-height:380px;font-family:Arial,sans-serif;line-height:1.55;color:var(--bs-body-color);background:var(--bs-body-bg)}
    .template-editor-shell .note-statusbar{background:var(--bs-tertiary-bg);border-top-color:var(--bs-border-color)}
    .template-sidebar{position:sticky;top:88px}
    .template-tag-search{border-color:var(--bs-border-color);border-radius:8px;font-size:13px}
    .template-tag-tabs{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:6px}
    .template-tag-tab{padding:8px;border:1px solid var(--bs-border-color);border-radius:8px;background:var(--bs-body-bg);color:var(--bs-secondary-color);font-size:12px;font-weight:700;text-align:center}
    .template-tag-tab.active{border-color:var(--bs-primary);background:var(--bs-primary);color:#fff}
    .template-tag-list{max-height:420px;overflow:auto;padding-right:4px}
    .template-tag-group{display:none}
    .template-tag-group.active{display:block}
    .template-tag-button{width:100%;padding:8px 10px;border:1px solid var(--bs-border-color);border-radius:8px;background:var(--bs-body-bg);color:var(--bs-body-color);font-family:var(--bs-font-monospace);font-size:12px;text-align:left;transition:border-color .15s,background .15s}
    .template-tag-button:hover{border-color:rgba(var(--bs-primary-rgb),.55);background:color-mix(in srgb,var(--bs-primary) 8%,transparent)}
    .template-tag-button small{color:var(--bs-secondary-color);font-family:var(--bs-body-font-family);font-size:11px}
    .template-preview{min-height:220px;max-height:420px;overflow:auto;padding:16px;border:1px solid var(--bs-border-color);border-radius:8px;background:#fff;color:#26313d}
    .template-company-values{max-height:260px;overflow:auto}
    .template-company-values code{color:var(--bs-primary)}
    .template-empty-tags{display:none;padding:16px;border:1px dashed var(--bs-border-color);border-radius:8px;color:var(--bs-secondary-color);font-size:13px;text-align:center}
    .template-hint{display:flex;gap:8px;align-items:flex-start;padding:10px 12px;border-radius:8px;background:color-mix(in srgb,var(--bs-primary) 8%,transparent);color:var(--bs-body-color);font-size:12.5px}
    .starter-chip{padding:6px 12px;border:1px solid var(--bs-border-color);border-radius:50rem;background:var(--bs-body-bg);color:var(--bs-body-color);font-size:12px;font-weight:700;white-space:nowrap}
    .starter-chip:hover{border-color:var(--bs-primary);color:var(--bs-primary)}
    .starter-chip.active{border-color:var(--bs-primary);background:rgba(var(--bs-primary-rgb),.1);color:var(--bs-primary)}
    @media (max-width:1199.98px){
        .template-sidebar{position:static}
        .template-tag-list{max-height:320px}
    }
</style>
@endpush
@section('content')
<div class="row g-3 template-builder">
    <div class="col-xl-8">
        <form class="template-main-card mb-0" method="POST"
              action="{{ $template->exists ? route('company.templates.update',$template) : route('company.templates.store') }}">
            @csrf
            @if($template->exists)@method('PUT')@endif

            {{-- Details --}}
            <div class="card form-card">
                <div class="card-header border-light">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-file-text"></i></div>
                        <div>
                            <h6>{{ $template->exists ? 'Edit Template' : 'Create Template' }}</h6>
                            <p>Name it, choose the document type, then design the body below.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label" for="name">Template Name <span class="text-danger">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   value="{{ old('name',$template->name) }}" placeholder="e.g. Standard Vehicle Invoice" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label" for="type">Document Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" @selected(old('type',$template->type) === $type)>
                                        {{ str($type)->replace('_',' ')->title() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_default" name="is_default" value="1"
                                       @checked(old('is_default',$template->is_default))>
                                <label class="form-check-label" for="is_default">Default for this type</label>
                            </div>
                            <p class="text-muted fs-12 mb-0 ms-4">New documents of this type will use this template.</p>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                                       @checked(old('is_active',$template->exists ? $template->is_active : true))>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <p class="text-muted fs-12 mb-0 ms-4">Inactive templates are hidden from document screens.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body designer --}}
            <div class="card form-card mb-0">
                <div class="card-header border-light justify-content-between">
                    <div class="form-section-title mb-0">
                        <div class="fs-icon"><i class="ti ti-brush"></i></div>
                        <div>
                            <h6>Design Body</h6>
                            <p>Rich text and HTML are both supported.</p>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-light" type="button" id="restore-starter">
                        <i class="ti ti-restore me-1"></i>Restore layout
                    </button>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                        <span class="text-muted fs-12 text-uppercase fw-bold me-1">Start from</span>
                        <button class="starter-chip" type="button" data-starter="invoice"><i class="ti ti-file-invoice me-1"></i>Invoice</button>
                        <button class="starter-chip" type="button" data-starter="expense"><i class="ti ti-receipt me-1"></i>Expense</button>
                        <button class="starter-chip" type="button" data-starter="report"><i class="ti ti-report-analytics me-1"></i>Report</button>
                        <button class="starter-chip" type="button" data-starter="blank"><i class="ti ti-file me-1"></i>Blank</button>
                    </div>

                    <div class="template-hint mb-3">
                        <i class="ti ti-bulb mt-1"></i>
                        <span>Click any tag on the right to drop it where your cursor is. The preview fills tags with sample values so you can see the final document.</span>
                    </div>

                    <div class="template-editor-shell">
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="18" required>{{ old('body',$template->body) }}</textarea>
                    </div>
                    @error('body')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between gap-2 flex-wrap">
                    <span class="text-muted fs-12"><i class="ti ti-info-circle me-1"></i>Tags are replaced with real values when a document is generated.</span>
                    <div class="d-flex gap-2">
                        <a class="btn btn-light" href="{{ route('company.templates.index') }}">Cancel</a>
                        <button class="btn btn-primary" type="submit">
                            <i class="ti ti-check me-1"></i>{{ $template->exists ? 'Update Template' : 'Save Template' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-xl-4">
        <div class="template-sidebar">
            {{-- Tag picker --}}
            <div class="card template-side-card">
                <div class="card-header border-light">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="form-section-title mb-0">
                            <div class="fs-icon"><i class="ti ti-tag"></i></div>
                            <div>
                                <h6>Insert Tags</h6>
                                <p>{{ collect($tags)->sum(fn($items) => count($items)) }} available</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="app-search mb-3">
                        <input class="form-control template-tag-search" id="template-tag-search" placeholder="Search tags..." type="search">
                        <i class="ti ti-search app-search-icon text-muted"></i>
                    </div>
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
                                        <button class="template-tag-button" type="button" data-tag="{{ $tag }}"
                                                data-search="{{ str($tag.' '.$description)->lower() }}" title="Insert {{ $tag }}">
                                            {{ $tag }}
                                            <small class="d-block">{{ $description }}</small>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="template-empty-tags" id="template-empty-tags">No matching tags found.</div>
                </div>
            </div>

            {{-- Live preview --}}
            <div class="card template-side-card">
                <div class="card-header border-light">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="form-section-title mb-0">
                            <div class="fs-icon"><i class="ti ti-eye"></i></div>
                            <div>
                                <h6>Live Preview</h6>
                                <p>Sample data</p>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-light" type="button" id="refresh-template-preview">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-preview" id="template-preview"></div>
                </div>
            </div>

            {{-- Company values --}}
            <div class="card template-side-card mb-0">
                <div class="card-header border-light">
                    <a class="d-flex align-items-center justify-content-between w-100 text-body text-decoration-none"
                       data-bs-toggle="collapse" href="#companyValues" role="button" aria-expanded="false">
                        <div class="form-section-title mb-0">
                            <div class="fs-icon"><i class="ti ti-building"></i></div>
                            <div>
                                <h6>Your Company Values</h6>
                                <p>What company tags resolve to</p>
                            </div>
                        </div>
                        <i class="ti ti-chevron-down"></i>
                    </a>
                </div>
                <div class="collapse" id="companyValues">
                    <div class="card-body d-grid gap-2 template-company-values">
                        @foreach($companyTags as $tag => $value)
                            <div>
                                <code>{{ $tag }}</code>
                                <small class="text-muted d-block">{{ filled($value) ? $value : '—' }}</small>
                            </div>
                        @endforeach
                    </div>
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
        '[warranty_period]' => '12 Months',
        '[warranty_status]' => 'Active',
        '[warranty_duration]' => '12 Months',
        '[warranty_start_date]' => now()->format('Y-m-d'),
        '[warranty_end_date]' => now()->addYear()->format('Y-m-d'),
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
        const isNew = @json(! $template->exists);

        const th = 'style="padding:8px 10px;border-bottom:1px solid #e6ebf2;font-size:12px;text-transform:uppercase;color:#64748b;text-align:left"';
        const td = 'style="padding:9px 10px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#152238"';
        const label = 'style="font-size:12px;color:#64748b;text-transform:uppercase"';
        const value = 'style="font-size:14px;font-weight:700;color:#152238"';

        const starters = {
            invoice: '<div style="font-family:Arial,sans-serif;color:#152238">'
                + '<div style="display:flex;justify-content:space-between;border-bottom:2px solid #1f55c8;padding-bottom:12px;margin-bottom:16px">'
                + '<div><h2 style="margin:0;font-size:22px">[company_name]</h2>'
                + '<p style="margin:4px 0 0;font-size:12px;color:#64748b">[company_address] &middot; [company_city]<br>[company_phone] &middot; [company_email]</p></div>'
                + '<div style="text-align:right"><h3 style="margin:0;font-size:18px;color:#1f55c8">INVOICE</h3>'
                + '<p style="margin:4px 0 0;font-size:12px;color:#64748b">[invoice_no]<br>[invoice_date]</p></div></div>'
                + '<table style="width:100%;border-collapse:collapse;margin-bottom:16px"><tr>'
                + '<td style="width:50%;vertical-align:top"><span ' + label + '>Buyer</span><br><span ' + value + '>[buyer_name]</span>'
                + '<p style="margin:4px 0 0;font-size:12px;color:#64748b">[buyer_phone]<br>[buyer_email]</p></td>'
                + '<td style="width:50%;vertical-align:top"><span ' + label + '>Seller</span><br><span ' + value + '>[seller_name]</span>'
                + '<p style="margin:4px 0 0;font-size:12px;color:#64748b">[seller_phone]</p></td></tr></table>'
                + '<table style="width:100%;border-collapse:collapse;margin-bottom:16px">'
                + '<thead><tr><th ' + th + '>Vehicle</th><th ' + th + '>Registration</th><th ' + th + '>VIN</th><th ' + th + '>Sale Type</th></tr></thead>'
                + '<tbody><tr><td ' + td + '>[vehicle]</td><td ' + td + '>[registration]</td><td ' + td + '>[vin]</td><td ' + td + '>[sale_type]</td></tr></tbody></table>'
                + '<table style="width:100%;border-collapse:collapse;margin-bottom:16px">'
                + '<thead><tr><th ' + th + '>Warranty</th><th ' + th + '>Duration</th><th ' + th + '>Start</th><th ' + th + '>End</th><th ' + th + '>Status</th></tr></thead>'
                + '<tbody><tr><td ' + td + '>[warranty_period]</td><td ' + td + '>[warranty_duration]</td><td ' + td + '>[warranty_start_date]</td><td ' + td + '>[warranty_end_date]</td><td ' + td + '>[warranty_status]</td></tr></tbody></table>'
                + '<table style="width:100%;border-collapse:collapse;max-width:360px;margin-left:auto">'
                + '<tr><td ' + td + '>Vehicle price</td><td ' + td + ' style="text-align:right">[vehicle_price]</td></tr>'
                + '<tr><td ' + td + '>Discount</td><td ' + td + ' style="text-align:right">[discount]</td></tr>'
                + '<tr><td ' + td + '>Commission</td><td ' + td + ' style="text-align:right">[commission]</td></tr>'
                + '<tr><td ' + td + '>Paid</td><td ' + td + ' style="text-align:right">[paid_amount]</td></tr>'
                + '<tr><td ' + td + ' style="font-weight:700">Balance</td><td ' + td + ' style="text-align:right;font-weight:700">[balance_amount]</td></tr>'
                + '<tr><td ' + td + '>Payment status</td><td ' + td + ' style="text-align:right">[payment_status]</td></tr></table>'
                + '<p style="margin-top:24px;font-size:11px;color:#94a3b8;text-align:center">Thank you for your business &middot; [company_name]</p></div>',
            expense: '<div style="font-family:Arial,sans-serif;color:#152238">'
                + '<div style="border-bottom:2px solid #16a34a;padding-bottom:12px;margin-bottom:16px">'
                + '<h2 style="margin:0;font-size:22px">[company_name]</h2>'
                + '<p style="margin:4px 0 0;font-size:12px;color:#64748b">[company_address] &middot; [company_phone]</p></div>'
                + '<h3 style="margin:0 0 12px;font-size:17px;color:#16a34a">EXPENSE RECEIPT</h3>'
                + '<table style="width:100%;border-collapse:collapse;margin-bottom:16px"><tr>'
                + '<td style="width:33%"><span ' + label + '>Date</span><br><span ' + value + '>[expense_date]</span></td>'
                + '<td style="width:33%"><span ' + label + '>Category</span><br><span ' + value + '>[expense_category]</span></td>'
                + '<td style="width:34%"><span ' + label + '>Payment</span><br><span ' + value + '>[payment]</span></td></tr></table>'
                + '<table style="width:100%;border-collapse:collapse;margin-bottom:16px">'
                + '<thead><tr><th ' + th + '>Items</th></tr></thead>'
                + '<tbody><tr><td ' + td + '>[expense_lists]</td></tr></tbody></table>'
                + '<p style="font-size:13px;color:#475569;margin-bottom:16px">[expense_description]</p>'
                + '<table style="width:100%;border-collapse:collapse;max-width:320px;margin-left:auto">'
                + '<tr><td ' + td + ' style="font-weight:700">Total expense</td><td ' + td + ' style="text-align:right;font-weight:700">[expense_amount]</td></tr></table>'
                + '<p style="margin-top:24px;font-size:11px;color:#94a3b8;text-align:center">[company_name] &middot; [company_email]</p></div>',
            report: '<div style="font-family:Arial,sans-serif;color:#152238">'
                + '<div style="border-bottom:2px solid #1f55c8;padding-bottom:12px;margin-bottom:16px">'
                + '<h2 style="margin:0;font-size:22px">[company_name]</h2>'
                + '<p style="margin:4px 0 0;font-size:12px;color:#64748b">[company_address] &middot; [company_phone] &middot; [company_email]</p></div>'
                + '<h3 style="margin:0 0 4px;font-size:18px">Report title</h3>'
                + '<p style="margin:0 0 16px;font-size:12px;color:#64748b">Prepared by [company_manager_name] &middot; [company_designation]</p>'
                + '<table style="width:100%;border-collapse:collapse;margin-bottom:16px">'
                + '<thead><tr><th ' + th + '>Description</th><th ' + th + '>Amount</th></tr></thead>'
                + '<tbody><tr><td ' + td + '>Row description</td><td ' + td + ' style="text-align:right">0.00</td></tr>'
                + '<tr><td ' + td + ' style="font-weight:700">Total</td><td ' + td + ' style="text-align:right;font-weight:700">0.00</td></tr></tbody></table>'
                + '<p style="font-size:12px;color:#64748b">Notes or terms go here.</p></div>',
            blank: '',
        };

        const starterForType = (type) => starters[type] !== undefined ? type : (/^(salary|profit_and_loss|balence_sheet)$/.test(type) ? 'report' : 'invoice');

        const hasSummernote = () => window.jQuery && jQuery.fn.summernote;
        const getBody = () => hasSummernote() ? jQuery(textarea).summernote('code') : textarea.value;
        const setBody = (html) => {
            if (hasSummernote()) jQuery(textarea).summernote('code', html);
            else textarea.value = html;
            updatePreview();
        };
        const plainText = (html) => {
            const holder = document.createElement('div');
            holder.innerHTML = html || '';
            return (holder.textContent || '').replace(/\s+/g, ' ').trim();
        };

        const updatePreview = () => {
            let html = getBody();
            Object.entries(sampleValues).forEach(([tag, tagValue]) => {
                html = html.split(tag).join(tagValue || '-');
            });
            preview.innerHTML = html || '<p style="margin:0;color:#94a3b8;font-size:13px">Nothing to preview yet. Pick a layout above or start typing.</p>';
        };

        const markStarter = (key) => {
            document.querySelectorAll('[data-starter]').forEach((chip) => {
                chip.classList.toggle('active', chip.dataset.starter === key);
            });
        };

        if (hasSummernote()) {
            jQuery(textarea).summernote({
                height: 460,
                placeholder: 'Design your invoice, expense or report template...',
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
            textarea.addEventListener('input', updatePreview);
        }

        document.querySelectorAll('[data-starter]').forEach((chip) => {
            chip.addEventListener('click', () => {
                const key = chip.dataset.starter;
                if (key !== 'blank' && plainText(getBody()) && !window.confirm('Replace the current design with the ' + key + ' layout?')) {
                    return;
                }
                setBody(key === 'blank' ? '' : starters[key]);
                markStarter(key);
            });
        });

        document.getElementById('restore-starter').addEventListener('click', () => {
            const key = starterForType(typeSelect.value);
            if (plainText(getBody()) && !window.confirm('Discard your changes and restore the ' + key + ' layout?')) return;
            setBody(starters[key]);
            markStarter(key);
        });

        document.querySelectorAll('.template-tag-button').forEach((button) => {
            button.addEventListener('click', () => {
                const tag = button.dataset.tag;
                if (hasSummernote()) {
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
            const targetGroup = document.querySelector('[data-tag-group="' + group + '"]') ? group : 'company';
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

        typeSelect.addEventListener('change', () => {
            activateTagGroup(typeSelect.value);
            if (isNew && !plainText(getBody())) {
                const key = starterForType(typeSelect.value);
                setBody(starters[key]);
                markStarter(key);
            }
        });

        activateTagGroup(typeSelect.value);
        document.getElementById('refresh-template-preview').addEventListener('click', updatePreview);

        if (isNew && !plainText(getBody())) {
            const key = starterForType(typeSelect.value);
            setBody(starters[key]);
            markStarter(key);
        } else {
            markStarter(starterForType(typeSelect.value));
            updatePreview();
        }

        textarea.closest('form').addEventListener('submit', function (event) {
            if (hasSummernote()) jQuery(textarea).val(jQuery(textarea).summernote('code'));
            if (!plainText(getBody())) {
                event.preventDefault();
                window.alert('Please add a template body before saving.');
            }
        });
    });
</script>
@endpush
