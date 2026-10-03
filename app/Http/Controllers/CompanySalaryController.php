<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanySalaryController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeSalary::with('employee')->where('branch_id', $this->companyId())
            ->when($request->month, fn ($q, $v) => $q->where('month', $this->monthStart($v)))
            ->when($request->employee_id, fn ($q, $v) => $q->where('employee_id', $v))
            ->when(in_array($request->status, ['pending', 'paid'], true), fn ($q) => $q->where('status', $request->status));

        $summary = (clone $query)
            ->selectRaw('count(*) records, coalesce(sum(net_salary),0) net,
                         coalesce(sum(case when status = "paid" then net_salary else 0 end),0) paid,
                         coalesce(sum(case when status = "pending" then net_salary else 0 end),0) pending')
            ->first();

        return view('company.salaries.index', [
            'salaries' => $query->latest('month')->orderBy('id')->paginate(20)->withQueryString(),
            'summary' => $summary,
            'months' => $this->months(),
            'employees' => $this->employees()->pluck('name', 'id'),
            'paymentMethods' => $this->paymentMethods(),
            'symbol' => $this->currencySymbol(),
        ]);
    }

    public function create(Request $request)
    {
        $month = $this->monthStart($request->month ?: now()->format('Y-m'));
        $employees = $this->employees()->get();
        $generated = EmployeeSalary::where('branch_id', $this->companyId())
            ->where('month', $month)->pluck('employee_id')->all();

        return view('company.salaries.generate', [
            'employees' => $employees,
            'generated' => $generated,
            'month' => $month->format('Y-m'),
            'symbol' => $this->currencySymbol(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer', Rule::exists('employees', 'id')->where('branch_id', $this->companyId())],
        ]);

        $month = $this->monthStart($data['month']);
        $selected = $data['employee_ids'] ?? [];

        $employees = $this->employees()
            ->when($selected, fn ($q) => $q->whereIn('id', $selected))
            ->get();

        $existing = EmployeeSalary::where('branch_id', $this->companyId())
            ->where('month', $month)
            ->pluck('employee_id')
            ->all();

        $created = 0;
        foreach ($employees as $employee) {
            if (in_array($employee->id, $existing, true)) {
                continue;
            }

            EmployeeSalary::create([
                'branch_id' => $this->companyId(),
                'employee_id' => $employee->id,
                'month' => $month,
                'basic_salary' => $employee->basic_salary,
                'allowances' => $employee->allowances,
                'deductions' => $employee->deductions,
                'status' => 'pending',
                'generated_by' => auth()->id(),
            ]);
            $created++;
        }

        $skipped = $employees->count() - $created;
        $message = $created
            ? "Generated {$created} salary record(s) for " . $month->format('F Y') . '.'
            : 'No new salary records — already generated for ' . $month->format('F Y') . '.';

        return redirect()->route('company.salaries.index', ['month' => $month->format('Y-m')])
            ->with($created ? 'success' : 'error', $message . ($skipped > 0 ? " ({$skipped} skipped)" : ''));
    }

    public function show(EmployeeSalary $salary)
    {
        $this->authorizeSalary($salary);

        return view('company.salaries.show', [
            'salary' => $salary->load('employee', 'generator'),
            'symbol' => $this->currencySymbol(),
        ]);
    }

    public function edit(EmployeeSalary $salary)
    {
        $this->authorizeSalary($salary);

        return view('company.salaries.form', [
            'salary' => $salary->load('employee'),
            'employees' => $this->employees(false)->pluck('name', 'id'),
            'months' => $this->months(),
            'paymentMethods' => $this->paymentMethods(),
            'symbol' => $this->currencySymbol(),
        ]);
    }

    public function update(Request $request, EmployeeSalary $salary)
    {
        $this->authorizeSalary($salary);

        $data = $request->validate([
            'employee_id' => ['required', Rule::exists('employees', 'id')->where('branch_id', $this->companyId())],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'bonus' => ['nullable', 'numeric', 'min:0'],
            'overtime' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
            'advance' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['pending', 'paid'])],
            'payment_method' => ['nullable', 'max:30'],
            'paid_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'max:2000'],
        ]);

        $request->validate([
            'month' => ['required', 'date_format:Y-m', Rule::unique('employee_salaries', 'month')
                ->where('employee_id', $data['employee_id'])
                ->where('branch_id', $this->companyId())
                ->ignore($salary)],
        ]);

        $salary->update($this->normalize($data, $request->input('month')));

        return redirect()->route('company.salaries.index')->with('success', 'Salary record updated.');
    }

    public function markPaid(Request $request, EmployeeSalary $salary)
    {
        $this->authorizeSalary($salary);

        $data = $request->validate([
            'payment_method' => ['nullable', 'max:30'],
            'paid_date' => ['nullable', 'date'],
        ]);

        $salary->update([
            'status' => 'paid',
            'payment_method' => $data['payment_method'] ?? $salary->payment_method,
            'paid_date' => $data['paid_date'] ?? now()->toDateString(),
        ]);

        return back()->with('success', "Salary for {$salary->employee->name} marked as paid.");
    }

    public function destroy(EmployeeSalary $salary)
    {
        $this->authorizeSalary($salary);
        $salary->delete();

        return redirect()->route('company.salaries.index')->with('success', 'Salary record deleted.');
    }

    private function normalize(array $data, string $month): array
    {
        foreach (['allowances', 'bonus', 'overtime', 'deductions', 'advance'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }

        $data['month'] = $this->monthStart($month);
        $data['branch_id'] = $this->companyId();

        return $data;
    }

    private function monthStart(string $month): Carbon
    {
        return Carbon::createFromFormat('Y-m', substr($month, 0, 7))->startOfMonth();
    }

    private function employees(bool $activeOnly = true)
    {
        return Employee::where('branch_id', $this->companyId())
            ->when($activeOnly, fn ($q) => $q->where('is_active', true))
            ->orderBy('name');
    }

    private function months()
    {
        return EmployeeSalary::where('branch_id', $this->companyId())
            ->select('month')
            ->distinct()
            ->orderByDesc('month')
            ->pluck('month')
            ->map(fn ($month) => Carbon::parse($month)->format('Y-m'));
    }

    private function paymentMethods()
    {
        return PaymentMethod::where(fn ($q) => $q->where('branch_id', $this->companyId())->orWhereNull('branch_id'))
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name');
    }

    private function companyId(): int
    {
        abort_unless(auth()->user()->branch_id, 403);

        return auth()->user()->branch_id;
    }

    private function authorizeSalary(EmployeeSalary $salary): void
    {
        abort_unless($salary->branch_id === $this->companyId(), 403);
    }
}
