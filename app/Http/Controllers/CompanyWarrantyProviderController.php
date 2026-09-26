<?php

namespace App\Http\Controllers;

use App\Models\WarrantyProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyWarrantyProviderController extends Controller
{
    public function index()
    {
        $this->ensureDefaults();

        return view('company.warranty_providers.index', [
            'providers' => WarrantyProvider::where('branch_id', $this->companyId())->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('company.warranty_providers.form', ['provider' => new WarrantyProvider(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        WarrantyProvider::create($this->validated($request));

        return redirect()->route('company.warranty-providers.index')->with('success', 'Warranty provider created.');
    }

    public function edit(WarrantyProvider $warranty_provider)
    {
        $this->authorizeProvider($warranty_provider);

        return view('company.warranty_providers.form', ['provider' => $warranty_provider]);
    }

    public function update(Request $request, WarrantyProvider $warranty_provider)
    {
        $this->authorizeProvider($warranty_provider);
        $warranty_provider->update($this->validated($request, $warranty_provider));

        return redirect()->route('company.warranty-providers.index')->with('success', 'Warranty provider updated.');
    }

    private function validated(Request $request, ?WarrantyProvider $provider = null): array
    {
        return $request->validate([
            'name' => ['required', 'max:120', Rule::unique('warranty_providers', 'name')->where('branch_id', $this->companyId())->ignore($provider)],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'branch_id' => $this->companyId(),
            'is_system' => $provider?->is_system ?? false,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function ensureDefaults(): void
    {
        foreach (['Handler Protect', 'Car Hive Ltd', 'Other / Custom Provider', 'No Warranty'] as $name) {
            WarrantyProvider::firstOrCreate(
                ['branch_id' => $this->companyId(), 'name' => $name],
                ['is_system' => true, 'is_active' => true]
            );
        }
    }

    private function companyId(): int
    {
        abort_unless(auth()->user()->branch_id, 403);

        return auth()->user()->branch_id;
    }

    private function authorizeProvider(WarrantyProvider $provider): void
    {
        abort_unless($provider->branch_id === $this->companyId(), 403);
    }
}
