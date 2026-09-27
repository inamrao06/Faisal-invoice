<?php

namespace App\Http\Controllers;

use App\Models\WarrantyDuration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyWarrantyDurationController extends Controller
{
    public function index()
    {
        $this->ensureDefaults();

        return view('company.warranty_durations.index', [
            'durations' => WarrantyDuration::where('branch_id', $this->companyId())->orderBy('months')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('company.warranty_durations.form', ['duration' => new WarrantyDuration(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        WarrantyDuration::create($this->validated($request));

        return redirect()->route('company.warranty-durations.index')->with('success', 'Warranty duration created.');
    }

    public function edit(WarrantyDuration $warranty_duration)
    {
        $this->authorizeDuration($warranty_duration);

        return view('company.warranty_durations.form', ['duration' => $warranty_duration]);
    }

    public function update(Request $request, WarrantyDuration $warranty_duration)
    {
        $this->authorizeDuration($warranty_duration);
        $warranty_duration->update($this->validated($request, $warranty_duration));

        return redirect()->route('company.warranty-durations.index')->with('success', 'Warranty duration updated.');
    }

    private function validated(Request $request, ?WarrantyDuration $duration = null): array
    {
        $data = $request->validate([
            'name' => ['nullable', 'max:120'],
            'months' => ['required', 'integer', 'min:0', 'max:120', Rule::unique('warranty_durations', 'months')->where('branch_id', $this->companyId())->ignore($duration)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['branch_id'] = $this->companyId();
        $data['name'] = $data['name'] ?: $data['months'] . ' month' . ((int) $data['months'] === 1 ? '' : 's');
        $data['is_system'] = $duration?->is_system ?? false;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function ensureDefaults(): void
    {
        foreach ([0, 3, 6, 12, 18, 24, 36, 48, 60] as $months) {
            WarrantyDuration::firstOrCreate(
                ['branch_id' => $this->companyId(), 'months' => $months],
                ['name' => $months . ' month' . ($months === 1 ? '' : 's'), 'is_system' => true, 'is_active' => true]
            );
        }
    }

    private function companyId(): int
    {
        abort_unless(auth()->user()->branch_id, 403);

        return auth()->user()->branch_id;
    }

    private function authorizeDuration(WarrantyDuration $duration): void
    {
        abort_unless($duration->branch_id === $this->companyId(), 403);
    }
}
