@extends('layouts.app')
@section('title', $company->exists ? 'Edit Company' : 'Create Company')
@section('page_title', $company->exists ? 'Edit Company' : 'Create Company')
@section('content')

<div class="frm-wrap">
<div class="frm-header">
    <div>
        <h2>{{ $company->exists ? 'Edit Company' : 'Create New Company' }}</h2>
        <p>{{ $company->exists ? 'Update the company profile, contact details and brand assets.' : 'Register a new company in the booking system. All marked fields are required.' }}</p>
    </div>
    <a href="{{ route('companies.index') }}" class="btn btn-light">
        <i class="ti ti-arrow-left"></i> Back
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
                <div class="frm-sec-icon blue"><i class="ti ti-building"></i></div>
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
                    <label class="frm-lbl" for="company_type">Company Type <span class="req">*</span></label>
                    <select id="company_type" class="form-select @error('type') is-invalid @enderror" name="type" required>
                        @foreach($types as $t)
                            <option value="{{ $t->code }}" @selected(old('type',$company->type)===$t->code)>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="frm-lbl" for="company_currency">Currency</label>
                    <select id="company_currency" class="form-select" name="currency_id" data-toggle="select2" data-placeholder="Select currency">
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
                <div class="frm-sec-icon teal"><i class="ti ti-mail"></i></div>
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
                <div class="frm-sec-icon violet"><i class="ti ti-id"></i></div>
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
                <div class="frm-sec-icon amber"><i class="ti ti-photo"></i></div>
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
                        <i class="ti ti-cloud-upload uz-icon"></i>
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
                        <i class="ti ti-pencil uz-icon"></i>
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
                        <i class="ti ti-award uz-icon"></i>
                        <p><strong>Click to upload</strong><br>Stamp / seal</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ⑤ STATUS --}}
        <div class="frm-section">
            <div class="frm-sec-title">
                <div class="frm-sec-icon green"><i class="ti ti-toggle-right"></i></div>
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
                <i class="ti ti-circle-check me-1"></i>
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
