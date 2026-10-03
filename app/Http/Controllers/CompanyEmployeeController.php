<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::where('branch_id', $this->companyId())
            ->withCount('salaries')
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w
                ->where('name', 'like', "%{$v}%")
                ->orWhere('employee_code', 'like', "%{$v}%")
                ->orWhere('phone', 'like', "%{$v}%")
                ->orWhere('designation', 'like', "%{$v}%")))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('company.employees.index', [
            'employees' => $employees,
            'symbol' => $this->currencySymbol(),
            'activeCount' => Employee::where('branch_id', $this->companyId())->where('is_active', true)->count(),
        ]);
    }

    public function create()
    {
        return view('company.employees.form', [
            'employee' => new Employee([
                'employee_code' => $this->nextCode(),
                'joining_date' => now()->toDateString(),
                'is_active' => true,
            ]),
            'symbol' => $this->currencySymbol(),
        ]);
    }

    public function store(Request $request)
    {
        Employee::create($this->validated($request));

        return redirect()->route('company.employees.index')->with('success', 'Employee created.');
    }

    public function show(Employee $employee)
    {
        $this->authorizeEmployee($employee);

        $salaries = $employee->salaries()->latest('month')->paginate(12);
        $totals = EmployeeSalary::where('employee_id', $employee->id)
            ->selectRaw('count(*) records, coalesce(sum(net_salary),0) total_net,
                         coalesce(sum(case when status = "paid" then net_salary else 0 end),0) paid_net,
                         coalesce(sum(case when status = "pending" then net_salary else 0 end),0) pending_net')
            ->first();

        return view('company.employees.show', [
            'employee' => $employee,
            'salaries' => $salaries,
            'totals' => $totals,
            'symbol' => $this->currencySymbol(),
        ]);
    }

    public function edit(Employee $employee)
    {
        $this->authorizeEmployee($employee);

        return view('company.employees.form', ['employee' => $employee, 'symbol' => $this->currencySymbol()]);
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeEmployee($employee);
        $employee->update($this->validated($request, $employee));

        return redirect()->route('company.employees.index')->with('success', 'Employee updated.');
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeEmployee($employee);

        if ($employee->salaries()->exists()) {
            return redirect()->route('company.employees.show', $employee)
                ->with('error', 'Delete this employee\'s salary records before removing the profile.');
        }

        $employee->delete();

        return redirect()->route('company.employees.index')->with('success', 'Employee deleted.');
    }

    private function validated(Request $request, ?Employee $employee = null): array
    {
        $data = $request->validate([
            'employee_code' => ['required', 'max:40', Rule::unique('employees', 'employee_code')
                ->where('branch_id', $this->companyId())->ignore($employee)],
            'name' => ['required', 'max:150'],
            'phone' => ['nullable', 'max:30'],
            'email' => ['nullable', 'email', 'max:190'],
            'cnic' => ['nullable', 'max:30'],
            'designation' => ['nullable', 'max:100'],
            'department' => ['nullable', 'max:100'],
            'address' => ['nullable', 'max:255'],
            'joining_date' => ['nullable', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'max:2000'],
        ]);

        return $data + [
            'branch_id' => $this->companyId(),
            'allowances' => $data['allowances'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function nextCode(): string
    {
        $sequence = Employee::where('branch_id', $this->companyId())->count();

        do {
            $sequence++;
            $code = 'EMP-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        } while (Employee::where('branch_id', $this->companyId())->where('employee_code', $code)->exists());

        return $code;
    }

    private function companyId(): int
    {
        abort_unless(auth()->user()->branch_id, 403);

        return auth()->user()->branch_id;
    }

    private function authorizeEmployee(Employee $employee): void
    {
        abort_unless($employee->branch_id === $this->companyId(), 403);
    }
}
