@extends('layouts.app')
@section('title', $company->exists ? 'Edit Company' : 'Create Company')
@section('page_title', $company->exists ? 'Edit Company' : 'Create Company')
@section('content')
<style>
.frm-wrap{max-width:1100px}
.frm-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.frm-header h2{margin:0;font-size:22px;font-weight:800;color:#0f172a}
.frm-header p{margin:4px 0 0;color:#64748b;font-size:13px}
.frm-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 4px 24px rgba(15,23,42,.06);margin-bottom:20px;overflow:hidden}
.frm-section{padding:24px 28px;border-bottom:1px solid #f1f5f9}
.frm-section:last-child{border-bottom:0}
.frm-sec-title{display:flex;align-items:center;gap:10px;margin-bottom:20px}
.frm-sec-icon{width:34px;height:34px;border-radius:9px;display:grid;place-items:center;font-size:15px;flex:0 0 auto}
.frm-sec-icon.blue{background:#dbeafe;color:#2563eb}
.frm-sec-icon.teal{background:#ccfbf1;color:#0d9488}
.frm-sec-icon.violet{background:#ede9fe;color:#7c3aed}
.frm-sec-icon.amber{background:#fef3c7;color:#d97706}
.frm-sec-icon.green{background:#dcfce7;color:#16a34a}
.frm-sec-title h4{margin:0;font-size:14px;font-weight:800;color:#0f172a}
.frm-sec-title p{margin:1px 0 0;font-size:12px;color:#94a3b8}
.frm-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.frm-grid .g2{grid-column:span 2}
.frm-grid .g3{grid-column:span 3}
.frm-lbl{display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:6px;letter-spacing:.01em}
.frm-lbl .req{color:#ef4444;margin-left:2px}
.form-control,.form-select{border-radius:8px;border:1px solid #d1d5db;font-size:14px;color:#1e293b;padding:9px 13px;transition:.15s}
.form-control:focus,.form-select:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.12)}
.form-control.is-invalid{border-color:#ef4444}
.upload-zone{border:2px dashed #cbd5e1;border-radius:10px;padding:20px 14px;text-align:center;cursor:pointer;background:#f8fafc;transition:.2s;position:relative;overflow:hidden}
.upload-zone:hover{border-color:#2563eb;background:#eff6ff}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
.upload-zone .uz-icon{font-size:28px;color:#cbd5e1;display:block;margin-bottom:6px;transition:.2s}
.upload-zone:hover .uz-icon{color:#2563eb}
.upload-zone p{margin:0;font-size:12px;color:#94a3b8;line-height:1.5}
.upload-zone strong{color:#2563eb}
.uz-preview{width:100%;max-height:88px;object-fit:contain;border-radius:7px;margin-bottom:8px;display:none}
.toggle-row{display:flex;align-items:center;gap:12px;padding:14px 18px;border-radius:10px;cursor:pointer;background:#f0fdf4;border:1px solid #bbf7d0;width:fit-content}
.toggle-row input[type=checkbox]{width:18px;height:18px;accent-color:#16a34a;cursor:pointer;flex:0 0 auto}
.toggle-row span{font-size:13px;font-weight:700;color:#15803d}
.frm-footer{padding:20px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;display:flex;gap:10px;align-items:center}
@media(max-width:860px){.frm-grid{grid-template-columns:1fr 1fr}.frm-grid .g2,.frm-grid .g3{grid-column:span 2}}
@media(max-width:560px){.frm-grid{grid-template-columns:1fr}.frm-grid .g2,.frm-grid .g3{grid-column:span 1}}
</style>

<div class="frm-wrap">
<div class="frm-header">
    <div>
        <h2>{{ $company->exists ? 'Edit Company' : 'Create New Company' }}</h2>
        <p>{{ $company->exists ? 'Update the company profile, contact details and brand assets.' : 'Register a new company in the booking system. All marked fields are required.' }}</p>
    </div>
    <a href="{{ route('companies.index') }}" class="btn btn-light">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<form method="POST" enctype="multipart/form-data"
      action="{{ $company->exists ? route('companies.update',$company) : route('companies.store') }}">
    @csrf
    @if($company->exists) @method('PUT') @endif

    {{-- ① IDENTITY --}}
    <div class="frm-card">
        <div class="frm-section">
            <div class="frm-sec-title">
                <div class="frm-sec-icon blue"><i class="bi bi-building"></i></div>
                <div><h4>Company Identity</h4><p>Core registration and business details</p></div>
            </div>
            <div class="frm-grid">
                <div>
                    <label class="frm-lbl">Company Name <span class="req">*</span></label>
                    <input class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name',$company->name) }}"
                           placeholder="e.g. Al-Noor Auto Services" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="frm-lbl">Company Code <span class="req">*</span></label>
                    <input class="form-control @error('code') is-invalid @enderror"
                           name="code" value="{{ old('code',$company->code) }}"
                           placeholder="e.g. ANAO" required>
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="frm-lbl">Company Type <span class="req">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" name="type" required>
                        @foreach($types as $t)
                            <option value="{{ $t->code }}" @selected(old('type',$company->type)===$t->code)>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="frm-lbl">Currency</label>
                    <select class="form-select" name="currency_id">
                        <option value="">— Select Currency —</option>
                        @foreach($currencies as $c)
                            <option value="{{ $c->id }}" @selected(old('currency_id',$company->currency_id)==$c->id)>
                                {{ $c->symbol }} {{ $c->code }} – {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="frm-lbl">Tax / Registration Number</label>
                    <input class="form-control" name="tax_number"
                           value="{{ old('tax_number',$company->tax_number) }}"
                           placeholder="NTN / STRN / GST No.">
                </div>
                <div>
                    <label class="frm-lbl">Invoice Prefix</label>
                    <input class="form-control" name="invoice_prefix"
                           value="{{ old('invoice_prefix',$company->invoice_prefix) }}"
                           placeholder="e.g. HO">
                </div>
            </div>
        </div>

        {{-- ② CONTACT --}}
        <div class="frm-section">
            <div class="frm-sec-title">
                <div class="frm-sec-icon teal"><i class="bi bi-envelope"></i></div>
                <div><h4>Contact Information</h4><p>Email, phone, address and web presence</p></div>
            </div>
            <div class="frm-grid">
                <div>
                    <label class="frm-lbl">Company Email</label>
                    <input class="form-control @error('company_email') is-invalid @enderror"
                           type="email" name="company_email"
                           value="{{ old('company_email',$company->company_email) }}"
                           placeholder="info@company.com">
                    @error('company_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="frm-lbl">Company Phone</label>
                    <input class="form-control" name="phone"
                           value="{{ old('phone',$company->phone) }}"
                           placeholder="+92 300 0000000">
                </div>
                <div>
                    <label class="frm-lbl">Website</label>
                    <input class="form-control @error('website') is-invalid @enderror"
                           type="url" name="website"
                           value="{{ old('website',$company->website) }}"
                           placeholder="https://www.company.com">
                    @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="frm-lbl">City</label>
                    <input class="form-control" name="city"
                           value="{{ old('city',$company->city) }}"
                           placeholder="Lahore">
                </div>
                <div class="g2">
                    <label class="frm-lbl">Company Address</label>
                    <textarea class="form-control" name="address" rows="2"
                              placeholder="Full registered address">{{ old('address',$company->address) }}</textarea>
                </div>
            </div>
        </div>

        {{-- ③ AUTHORIZED PERSON --}}
        <div class="frm-section">
            <div class="frm-sec-title">
                <div class="frm-sec-icon violet"><i class="bi bi-person-vcard"></i></div>
                <div><h4>Authorized Person</h4><p>Person authorized to sign and represent the company</p></div>
            </div>
            <div class="frm-grid">
                <div>
                    <label class="frm-lbl">Authorized Person</label>
                    <input class="form-control" name="authorized_person"
                           value="{{ old('authorized_person',$company->authorized_person) }}"
                           placeholder="Full legal name">
                </div>
                <div>
                    <label class="frm-lbl">Designation</label>
                    <input class="form-control" name="designation"
                           value="{{ old('designation',$company->designation) }}"
                           placeholder="e.g. CEO / Director">
                </div>
                <div>
                    <label class="frm-lbl">Manager / Contact Person</label>
                    <input class="form-control" name="manager_name"
                           value="{{ old('manager_name',$company->manager_name) }}"
                           placeholder="Operations manager name">
                </div>
            </div>
        </div>

        {{-- ④ BRAND ASSETS --}}
        <div class="frm-section">
            <div class="frm-sec-title">
                <div class="frm-sec-icon amber"><i class="bi bi-images"></i></div>
                <div><h4>Logo, Signature &amp; Stamp</h4><p>Upload company brand assets — PNG/JPG, max 2 MB each</p></div>
            </div>
            <div class="frm-grid">
                {{-- Logo --}}
                <div>
                    <label class="frm-lbl">Company Logo</label>
                    <div class="upload-zone" id="zone_logo">
                        <input type="file" name="logo" accept="image/*"
                               onchange="previewUpload(this,'prev_logo','zone_logo')">
                        @if($company->logo_path)
                            <img id="prev_logo" class="uz-preview"
                                 src="{{ asset('storage/'.$company->logo_path) }}" style="display:block">
                        @else
                            <img id="prev_logo" class="uz-preview">
                        @endif
                        <i class="bi bi-cloud-upload uz-icon"></i>
                        <p><strong>Click to upload</strong><br>Company logo</p>
                    </div>
                </div>
                {{-- Signature --}}
                <div>
                    <label class="frm-lbl">Authorized Signature</label>
                    <div class="upload-zone" id="zone_sig">
                        <input type="file" name="signature" accept="image/*"
                               onchange="previewUpload(this,'prev_sig','zone_sig')">
                        @if($company->signature_path)
                            <img id="prev_sig" class="uz-preview"
                                 src="{{ asset('storage/'.$company->signature_path) }}" style="display:block">
                        @else
                            <img id="prev_sig" class="uz-preview">
                        @endif
                        <i class="bi bi-pen uz-icon"></i>
                        <p><strong>Click to upload</strong><br>Signature image</p>
                    </div>
                </div>
                {{-- Stamp --}}
                <div>
                    <label class="frm-lbl">Company Stamp / Seal</label>
                    <div class="upload-zone" id="zone_stamp">
                        <input type="file" name="stamp" accept="image/*"
                               onchange="previewUpload(this,'prev_stamp','zone_stamp')">
                        @if($company->stamp_path)
                            <img id="prev_stamp" class="uz-preview"
                                 src="{{ asset('storage/'.$company->stamp_path) }}" style="display:block">
                        @else
                            <img id="prev_stamp" class="uz-preview">
                        @endif
                        <i class="bi bi-award uz-icon"></i>
                        <p><strong>Click to upload</strong><br>Stamp / seal</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ⑤ STATUS --}}
        <div class="frm-section">
            <div class="frm-sec-title">
                <div class="frm-sec-icon green"><i class="bi bi-toggle-on"></i></div>
                <div><h4>Status</h4><p>Inactive companies cannot be accessed by their admins</p></div>
            </div>
            <label class="toggle-row">
                <input type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $company->exists ? $company->is_active : true))>
                <span>Company is Active</span>
            </label>
        </div>

        <div class="frm-footer">
            <button class="btn btn-primary px-5" type="submit">
                <i class="bi bi-check2-circle me-1"></i>
                {{ $company->exists ? 'Update Company' : 'Create Company' }}
            </button>
            <a href="{{ route('companies.index') }}" class="btn btn-light">Cancel</a>
        </div>
    </div>
</form>
</div>

<script>
function previewUpload(input, previewId, zoneId) {
    const img  = document.getElementById(previewId);
    const zone = document.getElementById(zoneId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            img.src = e.target.result;
            img.style.display = 'block';
            zone.querySelector('.uz-icon').style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
