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

    <div class="uf-wrap">
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
                            <p>{{ $isSuper ? 'Assign company and access role' : 'Access role for ' . $myCompany?->name }}</p>
                        </div>
                    </div>
                    <div class="uf-grid">
                        <div>
                            <label class="uf-lbl" for="inp_branch">Assigned Company</label>
                            @if ($isSuper)
                                <select class="form-select @error('branch_id') is-invalid @enderror" id="inp_branch"
                                    name="branch_id" data-toggle="select2" data-placeholder="Select company">
                                    <option value="">— Select Company —</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" @selected($currentBranch == $company->id)>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @else
                                <div class="uf-static">
                                    <i class="ti ti-building"></i>
                                    <div>
                                        {{ $myCompany?->name ?? '—' }}
                                        <small>{{ $myCompany?->code }}</small>
                                    </div>
                                </div>
                                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                            @endif
                        </div>
                        <div>
                            <label class="uf-lbl" for="inp_role">Access Role</label>
                            <select class="form-select @error('company_role_num') is-invalid @enderror" id="inp_role"
                                name="company_role_num">
                                <option value="0" @selected($currentRoleNum === 0)>Full Access (all company modules)
                                </option>
                                @foreach ($companyRoles as $crole)
                                    @if ($isSuper || (int) $crole->branch_id === (int) auth()->user()->branch_id)
                                        <option value="{{ $crole->id }}" data-branch="{{ $crole->branch_id }}"
                                            @selected($currentRoleNum === (int) $crole->id)>
                                            {{ $crole->name }}{{ $isSuper ? ' — ' . ($companies->firstWhere('id', $crole->branch_id)?->name ?? '') : '' }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('company_role_num')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="role-info mt-3">
                        <i class="ti ti-info-circle-filled"></i>
                        <div>
                            <strong>Role Guide:</strong>
                            <strong style="color:#15803d">Full Access</strong> = unrestricted access to the company.
                            Any other role is restricted — define what it can do under
                            <a href="{{ route('company.permissions.index') }}"
                                style="color:#92400e;text-decoration:underline">Roles &amp; Permissions</a>.
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
