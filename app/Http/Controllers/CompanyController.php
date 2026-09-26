<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = $this->scope()->with('currency')->latest()->paginate(15);
        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        abort_unless(auth()->user()->isSuperUser(), 403);
        return view('companies.form', [
            'company'    => new Branch(['is_active' => true]),
            'types'      => $this->types(),
            'currencies' => $this->currencies(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isSuperUser(), 403);
        $data = $this->validated($request);

        if ($request->hasFile('logo'))      $data['logo_path']      = $request->file('logo')->store('company-logos', 'public');
        if ($request->hasFile('signature')) $data['signature_path'] = $request->file('signature')->store('company-signatures', 'public');
        if ($request->hasFile('stamp'))     $data['stamp_path']     = $request->file('stamp')->store('company-stamps', 'public');

        Branch::create($data);
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show(Branch $company)
    {
        $this->authorizeCompany($company);
        $company->load('currency', 'users');
        return view('companies.show', compact('company'));
    }

    public function edit(Branch $company)
    {
        $this->authorizeCompany($company);
        return view('companies.form', [
            'company'    => $company,
            'types'      => $this->types(),
            'currencies' => $this->currencies(),
        ]);
    }

    public function update(Request $request, Branch $company)
    {
        $this->authorizeCompany($company);
        $data = $this->validated($request, $company);

        if ($request->hasFile('logo'))      $data['logo_path']      = $request->file('logo')->store('company-logos', 'public');
        if ($request->hasFile('signature')) $data['signature_path'] = $request->file('signature')->store('company-signatures', 'public');
        if ($request->hasFile('stamp'))     $data['stamp_path']     = $request->file('stamp')->store('company-stamps', 'public');

        $company->update($data);
        return redirect()->route('companies.show', $company)->with('success', 'Company updated successfully.');
    }

    // ── Private helpers ────────────────────────────────────────────────

    private function validated(Request $request, ?Branch $company = null): array
    {
        return $request->validate([
            'code'              => ['required', 'max:30', Rule::unique('branches', 'code')->ignore($company)],
            'name'              => ['required', 'max:150'],
            'type'              => ['required', Rule::exists('branch_types', 'code')->where('is_active', true)],
            'currency_id'       => ['nullable', 'exists:currencies,id'],
            'email'             => ['nullable', 'email', 'max:120'],
            'company_email'     => ['nullable', 'email', 'max:150'],
            'phone'             => ['nullable', 'max:40'],
            'website'           => ['nullable', 'url', 'max:255'],
            'city'              => ['nullable', 'max:80'],
            'address'           => ['nullable', 'max:1000'],
            'manager_name'      => ['nullable', 'max:120'],
            'authorized_person' => ['nullable', 'max:150'],
            'designation'       => ['nullable', 'max:120'],
            'invoice_prefix'    => ['nullable', 'max:20'],
            'tax_number'        => ['nullable', 'max:80'],
            'logo'              => ['nullable', 'image', 'max:2048'],
            'signature'         => ['nullable', 'image', 'max:2048'],
            'stamp'             => ['nullable', 'image', 'max:2048'],
            'is_active'         => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }

    private function scope()
    {
        return Branch::query()->when(
            !auth()->user()->isSuperUser(),
            fn($q) => $q->whereKey(auth()->user()->branch_id ?? 0)
        );
    }

    private function authorizeCompany(Branch $company): void
    {
        abort_unless(
            auth()->user()->isSuperUser() || $company->id === auth()->user()->branch_id,
            403
        );
    }

    private function types()
    {
        return BranchType::where('is_active', true)->orderBy('name')->get();
    }

    private function currencies()
    {
        return Currency::where('is_active', true)->orderBy('code')->get();
    }
}
