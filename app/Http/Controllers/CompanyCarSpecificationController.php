<?php

namespace App\Http\Controllers;

use App\Models\CarSpecification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyCarSpecificationController extends Controller
{
    public function index(string $type)
    {
        $this->authorizeType($type);

        return view('company.car_specifications.index', [
            'type' => $type,
            'title' => CarSpecification::TYPES[$type],
            'specs' => CarSpecification::where('branch_id', $this->companyId())
                ->where('type', $type)
                ->orderBy('name')
                ->paginate(15),
        ]);
    }

    public function create(string $type)
    {
        $this->authorizeType($type);

        return view('company.car_specifications.form', [
            'type' => $type,
            'title' => CarSpecification::TYPES[$type],
            'spec' => new CarSpecification(['is_active' => true]),
        ]);
    }

    public function store(Request $request, string $type)
    {
        $this->authorizeType($type);
        CarSpecification::create($this->validated($request, $type));

        return redirect()->route('company.car-specifications.index', $type)->with('success', CarSpecification::TYPES[$type] . ' saved.');
    }

    public function edit(string $type, CarSpecification $specification)
    {
        $this->authorizeSpec($type, $specification);

        return view('company.car_specifications.form', [
            'type' => $type,
            'title' => CarSpecification::TYPES[$type],
            'spec' => $specification,
        ]);
    }

    public function update(Request $request, string $type, CarSpecification $specification)
    {
        $this->authorizeSpec($type, $specification);
        $specification->update($this->validated($request, $type, $specification));

        return redirect()->route('company.car-specifications.index', $type)->with('success', CarSpecification::TYPES[$type] . ' updated.');
    }

    private function validated(Request $request, string $type, ?CarSpecification $spec = null): array
    {
        return $request->validate([
            'name' => ['required', 'max:120', Rule::unique('car_specifications', 'name')->where('branch_id', $this->companyId())->where('type', $type)->ignore($spec)],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'branch_id' => $this->companyId(),
            'type' => $type,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function authorizeType(string $type): void
    {
        abort_unless(array_key_exists($type, CarSpecification::TYPES), 404);
    }

    private function authorizeSpec(string $type, CarSpecification $spec): void
    {
        $this->authorizeType($type);
        abort_unless($spec->branch_id === $this->companyId() && $spec->type === $type, 403);
    }

    private function companyId(): int
    {
        abort_unless(auth()->user()->branch_id, 403);

        return auth()->user()->branch_id;
    }
}
