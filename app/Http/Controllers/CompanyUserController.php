<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\CompanyRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CompanyUserController extends Controller
{
    public function index(Request $request)
    {
        $users = $this->scope()
            ->with('company')
            ->when($request->q, fn ($q, $v) => $q->where(fn ($x) => $x->where('name', 'like', "%$v%")->orWhere('email', 'like', "%$v%")
            ))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('company.users.index', compact('users'));
    }

    public function create()
    {
        return view('company.users.form', [
            'user' => new User(['is_active' => true, 'user_type' => 'admin', 'company_role_num' => 0]),
            'companies' => $this->companies(),
            'companyRoles' => $this->companyRoles(),
        ]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('company.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $this->authorizeUser($user);

        return view('users.form', [
            'user' => $user,
            'companies' => $this->companies(),
            'companyRoles' => $this->companyRoles(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeUser($user);
        $user->update($this->validated($request, $user));

        return redirect()->route('company.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorizeUser($user);
        abort_if($user->is(auth()->user()), 422, 'You cannot disable your own account.');
        $user->update(['is_active' => false]);

        return back()->with('success', 'User disabled.');
    }

    // ── Private helpers ────────────────────────────────────────────────

    private function validated(Request $request, ?User $user = null): array
    {
        $isSuperAdmin = auth()->user()->isSuperUser();
        $allowedTypes = $isSuperAdmin ? User::USER_TYPES : ['admin'];

        $data = $request->validate([
            'name' => ['required', 'max:150'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'max:30'],
            'user_type' => ['required', Rule::in($allowedTypes)],
            'company_role_num' => ['nullable', 'integer', 'min:0', 'max:255'],
            'branch_id' => ['nullable', Rule::in($this->companies()->pluck('id')->all())],
            'password' => [$user ? 'nullable' : 'required', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Keep legacy role column in sync
        $data['role'] = $data['user_type'];

        // Super admins have no company restriction
        if (($data['user_type'] ?? 'admin') === 'super_admin') {
            $data['branch_id'] = null;
            $data['company_role_num'] = 0;
        }

        // Non-super-admins can only assign to their own company
        if (! $isSuperAdmin) {
            $data['branch_id'] = auth()->user()->branch_id;
        }

        $data['company_role_num'] = (int) ($data['company_role_num'] ?? 0);

        if ($data['company_role_num'] > 0) {
            $roleExists = CompanyRole::whereKey($data['company_role_num'])
                ->where('is_active', true)
                ->when($data['branch_id'] !== null, fn ($q) => $q->where('branch_id', $data['branch_id']))
                ->exists();

            if (! $roleExists) {
                throw ValidationException::withMessages([
                    'company_role_num' => 'The selected role is not available for this company.',
                ]);
            }
        }
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function scope()
    {
        return User::query()->when(
            ! auth()->user()->isSuperUser(),
            fn ($q) => $q->where('branch_id', auth()->user()->branch_id ?? 0)
        );
    }

    private function companies()
    {
        return Branch::where('is_active', true)
            ->when(! auth()->user()->isSuperUser(), fn ($q) => $q->whereKey(auth()->user()->branch_id ?? 0))
            ->orderBy('name')->get();
    }

    private function companyRoles()
    {
        return CompanyRole::where('is_active', true)
            ->when(! auth()->user()->isSuperUser(), fn ($q) => $q->where('branch_id', auth()->user()->branch_id))
            ->orderBy('name')
            ->get();
    }

    private function authorizeUser(User $user): void
    {
        abort_unless(
            auth()->user()->isSuperUser() || $user->branch_id === auth()->user()->branch_id,
            403
        );
    }
}
