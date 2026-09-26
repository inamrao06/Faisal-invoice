@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Create User')
@section('page_title', $user->exists ? 'Edit User' : 'Create User')
@section('content')
    @php
        $isSuper = auth()->user()->isSuperUser();
        $currentRoleNum = (int) old('company_role_num', $user->company_role_num ?? 0);
        $currentBranch = old('branch_id', $user->branch_id ?? ($isSuper ? null : auth()->user()->branch_id));
        $myCompany = $companies->firstWhere('id', (int) auth()->user()->branch_id);
    @endphp
    <style>
        .uf-wrap {
            max-width: 860px
        }

        .uf-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap
        }

        .uf-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            color: #0f172a
        }

        .uf-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 13px
        }

        .uf-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 24px rgba(15, 23, 42, .06);
            overflow: hidden;
            margin-bottom: 0
        }

        .uf-section {
            padding: 24px 28px;
            border-bottom: 1px solid #f1f5f9
        }

        .uf-section:last-child {
            border-bottom: 0
        }

        .uf-sec-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px
        }

        .uf-sec-icon {
            width: 34px;
            height: 34px;
            border-radius: 9px;
            display: grid;
            place-items: center;
            font-size: 15px;
            flex: 0 0 auto
        }

        .uf-sec-icon.blue {
            background: #dbeafe;
            color: #2563eb
        }

        .uf-sec-icon.violet {
            background: #ede9fe;
            color: #7c3aed
        }

        .uf-sec-icon.green {
            background: #dcfce7;
            color: #16a34a
        }

        .uf-sec-title h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a
        }

        .uf-sec-title p {
            margin: 1px 0 0;
            font-size: 12px;
            color: #94a3b8
        }

        .uf-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px
        }

        .uf-grid .g2 {
            grid-column: span 2
        }

        .uf-lbl {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            letter-spacing: .01em
        }

        .uf-lbl .req {
            color: #ef4444;
            margin-left: 2px
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            color: #1e293b;
            padding: 9px 13px;
            transition: .15s
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12)
        }

        /* User type cards */
        .type-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        .type-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 16px;
            cursor: pointer;
            transition: .18s;
            position: relative;
            user-select: none
        }

        .type-card:hover {
            border-color: #93c5fd;
            background: #f0f9ff
        }

        .type-card input[type=radio] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0
        }

        .type-card.selected {
            border-color: #2563eb;
            background: #eff6ff
        }

        .type-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 20px;
            margin-bottom: 10px;
            transition: .18s
        }

        .type-card.super .type-card-icon {
            background: #fef3c7;
            color: #d97706
        }

        .type-card.admin .type-card-icon {
            background: #dbeafe;
            color: #2563eb
        }

        .type-card strong {
            display: block;
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 2px
        }

        .type-card span {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5
        }

        .type-card .check-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            position: absolute;
            top: 14px;
            right: 14px;
            transition: .18s;
            display: grid;
            place-items: center
        }

        .type-card.selected .check-dot {
            background: #2563eb;
            border-color: #2563eb
        }

        .type-card.selected .check-dot::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #fff;
            display: block
        }

        /* Role number field */
        .role-info {
            background: #fef9ec;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 12px 14px;
            font-size: 13px;
            color: #92400e;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 10px
        }

        .role-info i {
            margin-top: 1px;
            flex: 0 0 auto
        }

        /* Status toggle */
        .status-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border-radius: 10px;
            cursor: pointer;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            width: fit-content
        }

        .status-toggle input {
            width: 18px;
            height: 18px;
            accent-color: #16a34a;
            cursor: pointer
        }

        .status-toggle span {
            font-size: 13px;
            font-weight: 700;
            color: #15803d
        }

        .uf-footer {
            padding: 20px 28px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            align-items: center
        }

        .company-field {
            transition: .3s
        }

        .uf-static {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: #f8fafc;
            font-size: 14px;
            font-weight: 600;
            color: #1e293b
        }

        .uf-static i {
            color: #2563eb;
            font-size: 16px
        }

        .uf-static small {
            display: block;
            font-weight: 400;
            color: #94a3b8;
            font-size: 11px
        }

        @media(max-width:600px) {
            .uf-grid {
                grid-template-columns: 1fr
            }

            .uf-grid .g2 {
                grid-column: span 1
            }

            .type-cards {
                grid-template-columns: 1fr
            }
        }
    </style>

    <div class="panel">
        <div class="uf-header">
            <div>
                <h2>{{ $user->exists ? 'Edit User' : 'Create New User' }}</h2>
                <p>
                    @if ($user->exists)
                        Update account details, access type and company assignment.
                    @elseif($isSuper)
                        Super Admin can create both Super Admin and Company Admin users.
                    @else
                        Create a user for {{ $myCompany?->name ?? 'your company' }} and choose their access role.
                    @endif
                </p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-light">
                <i class="ti ti-arrow-left"></i> Back
            </a>
        </div>

        <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" id="userForm">
            @csrf
            @if ($user->exists)
                @method('PUT')
            @endif

            <div class="uf-card">

                {{-- ① USER TYPE --}}
                @php $currentType = old('user_type', $user->user_type ?? 'admin'); @endphp

                @if ($isSuper)
                    <div class="uf-section">
                        <div class="uf-sec-title">
                            <div class="uf-sec-icon violet"><i class="ti ti-shield-lock"></i></div>
                            <div>
                                <h4>User Type</h4>
                                <p>Determines global access scope</p>
                            </div>
                        </div>

                        <div class="type-cards">
                            {{-- Super Admin --}}
                            <label class="type-card super {{ $currentType === 'super_admin' ? 'selected' : '' }}"
                                id="card_super" for="type_super">
                                <input type="radio" id="type_super" name="user_type" value="super_admin"
                                    {{ $currentType === 'super_admin' ? 'checked' : '' }} onchange="onTypeChange(this)">
                                <div class="check-dot"></div>
                                <div class="type-card-icon"><i class="ti ti-shield-star"></i></div>
                                <strong>Super Admin</strong>
                                <span>Full system access.<br>Not tied to any company.</span>
                            </label>

                            {{-- Admin --}}
                            <label class="type-card admin {{ $currentType === 'admin' ? 'selected' : '' }}" id="card_admin"
                                for="type_admin">
                                <input type="radio" id="type_admin" name="user_type" value="admin"
                                    {{ $currentType === 'admin' ? 'checked' : '' }} onchange="onTypeChange(this)">
                                <div class="check-dot"></div>
                                <div class="type-card-icon"><i class="ti ti-building-check"></i></div>
                                <strong>Company Admin</strong>
                                <span>Scoped to one company.<br>Role 0 = full access.</span>
                            </label>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="user_type" value="admin">
                @endif

                {{-- ② PROFILE --}}
                <div class="uf-section">
                    <div class="uf-sec-title">
                        <div class="uf-sec-icon blue"><i class="ti ti-user"></i></div>
                        <div>
                            <h4>Profile Details</h4>
                            <p>Name, email, phone and password</p>
                        </div>
                    </div>
                    <div class="uf-grid">
                        <div>
                            <label class="uf-lbl" for="inp_name">Full Name <span class="req">*</span></label>
                            <input class="form-control @error('name') is-invalid @enderror" id="inp_name" name="name"
                                value="{{ old('name', $user->name) }}" placeholder="e.g. Ahmad Khan" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="uf-lbl" for="inp_email">Email Address <span class="req">*</span></label>
                            <input class="form-control @error('email') is-invalid @enderror" id="inp_email" type="email"
                                name="email" value="{{ old('email', $user->email) }}" placeholder="user@example.com"
                                required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="uf-lbl" for="inp_phone">Phone</label>
                            <input class="form-control" id="inp_phone" name="phone"
                                value="{{ old('phone', $user->phone) }}" placeholder="+92 300 0000000">
                        </div>
                        <div>
                            <label class="uf-lbl" for="inp_pass">
                                {{ $user->exists ? 'New Password' : 'Password' }}
                                @if (!$user->exists)
                                    <span class="req">*</span>
                                @endif
                            </label>
                            <input class="form-control @error('password') is-invalid @enderror" id="inp_pass"
                                type="password" name="password"
                                placeholder="{{ $user->exists ? 'Leave blank to keep current' : 'Minimum 8 characters' }}"
                                {{ $user->exists ? '' : 'required' }}>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="uf-lbl" for="inp_pass2">Confirm Password</label>
                            <input class="form-control" id="inp_pass2" type="password" name="password_confirmation"
                                placeholder="Repeat password">
                        </div>
                    </div>
                </div>

                {{-- ③ COMPANY & ROLE --}}
                <div class="uf-section company-field" id="companySection">
                    <div class="uf-sec-title">
                        <div class="uf-sec-icon blue"><i class="ti ti-buildings"></i></div>
                        <div>
                            <h4>Company &amp; Access Role</h4>
                            <p>Assign company and set role number (0 = full access)</p>
                        </div>
                    </div>
                    <div class="uf-grid">
                        <div>
                            <label class="uf-lbl" for="inp_branch">Assigned Company</label>
                            <select class="form-select @error('branch_id') is-invalid @enderror" id="inp_branch"
                                name="branch_id">
                                <option value="">— Select Company —</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}" @selected(old('branch_id', $user->branch_id) == $company->id)>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="uf-lbl" for="inp_role">
                                Role Number
                                <span style="font-weight:400;color:#94a3b8;font-size:11px">(0 = Full Access)</span>
                            </label>
                            <input class="form-control @error('company_role_num') is-invalid @enderror" id="inp_role"
                                type="number" name="company_role_num" min="0" max="255"
                                value="{{ old('company_role_num', $user->company_role_num ?? 0) }}" placeholder="0">
                            @error('company_role_num')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="role-info mt-3">
                        <i class="ti ti-info-circle-filled"></i>
                        <div>
                            <strong>Role Number Guide:</strong>
                            <strong style="color:#15803d"> 0</strong> = Full company access (same as Company Admin).
                            Any other number (e.g. <strong>1</strong>, <strong>2</strong>) = restricted role — use with
                            Roles &amp; Permissions to define what they can do.
                        </div>
                    </div>
                </div>

                {{-- ④ STATUS --}}
                <div class="uf-section">
                    <div class="uf-sec-title">
                        <div class="uf-sec-icon green"><i class="ti ti-toggle-right"></i></div>
                        <div>
                            <h4>Account Status</h4>
                            <p>Inactive users cannot log in</p>
                        </div>
                    </div>
                    <label class="status-toggle">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->exists ? $user->is_active : true))>
                        <span>Account is Active</span>
                    </label>
                </div>

                <div class="uf-footer">
                    <button class="btn btn-primary px-5" type="submit">
                        <i class="ti ti-circle-check me-1"></i>
                        {{ $user->exists ? 'Update User' : 'Create User' }}
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-light">Cancel</a>
                </div>

            </div>
        </form>
    </div>

    <script>
        function onTypeChange(radio) {
            // highlight selected card
            document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
            radio.closest('.type-card').classList.add('selected');

            // show/hide company section
            const isSuperAdmin = radio.value === 'super_admin';
            const section = document.getElementById('companySection');
            section.style.opacity = isSuperAdmin ? '.4' : '1';
            section.style.pointerEvents = isSuperAdmin ? 'none' : 'auto';

            if (isSuperAdmin) {
                document.getElementById('inp_branch').value = '';
                document.getElementById('inp_role').value = '0';
            }
        }

        // run on page load to sync initial state
        document.addEventListener('DOMContentLoaded', function() {
            const checked = document.querySelector('input[name=user_type]:checked');
            if (checked) onTypeChange(checked);
        });
    </script>
@endsection
