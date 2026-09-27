@extends('layouts.app')
@section('title', 'Company Settings')
@section('page_title', 'Company Settings')

@push('styles')
@endpush

@section('content')
@if(session('success'))
<div class="flash success"><i class="ti ti-circle-check-filled"></i>{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="flash error"><i class="ti ti-alert-triangle-filled"></i>{{ $errors->first() }}</div>
@endif

<div class="settings-wrap">
    <div class="tab-nav">
        <div class="tab-nav-head"><h4>Settings</h4></div>
        <a href="#tab-company" class="tab-link active" data-tab="tab-company"><i class="ti ti-building"></i> Company Details</a>
        <a href="#tab-smtp" class="tab-link" data-tab="tab-smtp"><i class="ti ti-mail-cog"></i> Mail / SMTP</a>
    </div>

    <div>
        <div id="tab-company" class="tab-panel active">
            <form method="POST" enctype="multipart/form-data" action="{{ route('company.settings.profile') }}">
                @csrf
                @method('PUT')

                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#eff6ff;color:#2563eb"><i class="ti ti-building"></i></div>
                        <div><h3>Company Identity</h3><p>Details shown on dashboards, reports and invoices</p></div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div>
                                <label>Company Name <span class="text-danger">*</span></label>
                                <input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $company->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label>Company Email</label>
                                <input class="form-control @error('company_email') is-invalid @enderror" type="email" name="company_email" value="{{ old('company_email', $company->company_email) }}">
                                @error('company_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label>Phone</label>
                                <input class="form-control" name="phone" value="{{ old('phone', $company->phone) }}">
                            </div>
                            <div>
                                <label>Website</label>
                                <input class="form-control @error('website') is-invalid @enderror" type="url" name="website" value="{{ old('website', $company->website) }}">
                                @error('website')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div>
                                <label>City</label>
                                <input class="form-control" name="city" value="{{ old('city', $company->city) }}">
                            </div>
                            <div>
                                <label>Tax Number</label>
                                <input class="form-control" name="tax_number" value="{{ old('tax_number', $company->tax_number) }}">
                            </div>
                            <div>
                                <label>Invoice Prefix</label>
                                <input class="form-control" name="invoice_prefix" value="{{ old('invoice_prefix', $company->invoice_prefix) }}">
                                <span class="hint">Used when generating invoice numbers</span>
                            </div>
                            <div>
                                <label>Authorized Person</label>
                                <input class="form-control" name="authorized_person" value="{{ old('authorized_person', $company->authorized_person) }}">
                            </div>
                            <div>
                                <label>Designation</label>
                                <input class="form-control" name="designation" value="{{ old('designation', $company->designation) }}">
                            </div>
                            <div>
                                <label>Manager / Contact Person</label>
                                <input class="form-control" name="manager_name" value="{{ old('manager_name', $company->manager_name) }}">
                            </div>
                            <div class="span-2">
                                <label>Address</label>
                                <textarea class="form-control" name="address" rows="2">{{ old('address', $company->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#faf5ff;color:#7c3aed"><i class="ti ti-photo"></i></div>
                        <div><h3>Brand Assets</h3><p>Logo, authorized signature and company stamp</p></div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div>
                                <label for="logo">Logo</label>
                                <input id="logo" class="form-control" type="file" name="logo" accept="image/*" data-image-preview="logoPreview">
                                <div class="logo-box">
                                    @if($company->logo_path)
                                        <img id="logoPreview" src="{{ asset('storage/'.$company->logo_path) }}" alt="Logo">
                                    @else
                                        <img id="logoPreview" src="{{ asset('paces/assets/images/logo.png') }}" alt="Logo">
                                    @endif
                                    <div><strong>Logo preview</strong><small id="logoPreviewName">Current logo</small></div>
                                </div>
                            </div>
                            <div>
                                <label for="signature">Signature</label>
                                <input id="signature" class="form-control" type="file" name="signature" accept="image/*" data-image-preview="signaturePreview">
                                <div class="logo-box">
                                    @if($company->signature_path)
                                        <img id="signaturePreview" src="{{ asset('storage/'.$company->signature_path) }}" alt="Signature">
                                    @else
                                        <img id="signaturePreview" src="{{ asset('paces/assets/images/sign.png') }}" alt="Signature">
                                    @endif
                                    <div><strong>Signature preview</strong><small id="signaturePreviewName">Current signature</small></div>
                                </div>
                            </div>
                            <div class="span-2">
                                <label for="stamp">Stamp</label>
                                <input id="stamp" class="form-control" type="file" name="stamp" accept="image/*" data-image-preview="stampPreview">
                                <div class="logo-box">
                                    @if($company->stamp_path)
                                        <img id="stampPreview" src="{{ asset('storage/'.$company->stamp_path) }}" alt="Stamp">
                                    @else
                                        <img id="stampPreview" src="{{ asset('paces/assets/images/checkmark.png') }}" alt="Stamp">
                                    @endif
                                    <div><strong>Stamp preview</strong><small id="stampPreviewName">Current stamp</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>Save Company Details</button>
                    <span style="font-size:12px;color:#94a3b8">Updates only your company profile</span>
                </div>
            </form>
        </div>

        <div id="tab-smtp" class="tab-panel">
            <form method="POST" action="{{ route('company.settings.smtp') }}">
                @csrf
                @method('PUT')
                <div class="s-card">
                    <div class="s-card-head">
                        <div class="card-icon" style="background:#fff7ed;color:#ea580c"><i class="ti ti-mail-cog"></i></div>
                        <div><h3>Mail / SMTP Configuration</h3><p>Company-specific outgoing email credentials</p></div>
                    </div>
                    <div class="s-card-body">
                        <div class="fg">
                            <div class="span-2">
                                <label>Mail Driver</label>
                                <select class="form-select" name="smtp_mailer" id="companyMailer">
                                    <option value="smtp" @selected(old('smtp_mailer', $company->smtp_mailer ?? 'smtp') === 'smtp')>SMTP</option>
                                    <option value="log" @selected(old('smtp_mailer', $company->smtp_mailer) === 'log')>Log</option>
                                </select>
                            </div>
                            <div id="companySmtpFields" class="span-2 fg" style="grid-column:span 2;padding:0;gap:14px 18px">
                                <div>
                                    <label>SMTP Host</label>
                                    <input class="form-control" name="smtp_host" value="{{ old('smtp_host', $company->smtp_host) }}" placeholder="smtp.example.com">
                                </div>
                                <div>
                                    <label>SMTP Port</label>
                                    <input class="form-control" type="number" name="smtp_port" value="{{ old('smtp_port', $company->smtp_port ?? 587) }}">
                                </div>
                                <div>
                                    <label>Username</label>
                                    <input class="form-control" name="smtp_username" value="{{ old('smtp_username', $company->smtp_username) }}">
                                </div>
                                <div>
                                    <label>Password</label>
                                    <input class="form-control" type="password" name="smtp_password" placeholder="{{ $company->smtp_password ? 'Leave blank to keep current' : '' }}" autocomplete="new-password">
                                </div>
                                <div>
                                    <label>Encryption</label>
                                    <select class="form-select" name="smtp_encryption">
                                        <option value="" @selected(old('smtp_encryption', $company->smtp_encryption) === null || old('smtp_encryption', $company->smtp_encryption) === '')>None</option>
                                        <option value="tls" @selected(old('smtp_encryption', $company->smtp_encryption ?? 'tls') === 'tls')>TLS</option>
                                        <option value="ssl" @selected(old('smtp_encryption', $company->smtp_encryption) === 'ssl')>SSL</option>
                                    </select>
                                </div>
                                <div></div>
                            </div>

                            <hr class="section-sep span-2">

                            <div>
                                <label>From Address</label>
                                <input class="form-control" type="email" name="smtp_from_address" value="{{ old('smtp_from_address', $company->smtp_from_address ?? $company->company_email) }}">
                            </div>
                            <div>
                                <label>From Name</label>
                                <input class="form-control" name="smtp_from_name" value="{{ old('smtp_from_name', $company->smtp_from_name ?? $company->name) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-footer">
                    <button class="btn btn-primary px-4" type="submit"><i class="ti ti-check me-1"></i>Save SMTP Settings</button>
                    <span style="font-size:12px;color:#94a3b8">Password remains unchanged when left blank</span>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.tab-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var tab = this.dataset.tab;
        document.querySelectorAll('.tab-link').forEach(function(l){ l.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById(tab).classList.add('active');
        history.replaceState(null, '', '#' + tab);
    });
});
(function(){
    var hash = location.hash.replace('#','');
    if(hash && document.getElementById(hash)){
        document.querySelectorAll('.tab-link').forEach(function(l){ l.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });
        document.querySelector('[data-tab="'+hash+'"]').classList.add('active');
        document.getElementById(hash).classList.add('active');
    }
})();
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
var mailSel = document.getElementById('companyMailer');
function checkCompanyMailer(){
    var smtp = document.getElementById('companySmtpFields');
    if(!smtp) return;
    smtp.style.opacity = mailSel && mailSel.value === 'smtp' ? '1' : '.4';
}
if(mailSel){ mailSel.addEventListener('change', checkCompanyMailer); checkCompanyMailer(); }
</script>
@endpush
