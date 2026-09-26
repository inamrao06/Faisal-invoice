<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\PaymentMethod;
use App\Models\WarrantyProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CompanyExpenseController extends Controller
{
    public function index()
    {
        return view('company.expenses.index', ['expenses' => Expense::with('head')->where('branch_id', $this->companyId())->latest('expense_date')->paginate(15)]);
    }

    public function create()
    {
        return view('company.expenses.form', ['expense' => new Expense(['expense_date' => now(), 'status' => 'submitted']), 'heads' => $this->heads(), 'paymentMethods' => $this->paymentMethods(), 'warrantyProviders' => $this->warrantyProviders()]);
    }

    public function store(Request $request)
    {
        $expense = DB::transaction(fn () => $this->save($request, $this->validated($request)));

        return redirect()->route('company.expenses.show', $expense)->with('success', 'Expense created.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeExpense($expense);
        $expense->load('items');

        return view('company.expenses.form', ['expense' => $expense, 'heads' => $this->heads(), 'paymentMethods' => $this->paymentMethods(), 'warrantyProviders' => $this->warrantyProviders()]);
    }

    public function update(Request $request, Expense $expense)
    {
        $this->authorizeExpense($expense);
        DB::transaction(fn () => $this->save($request, $this->validated($request), $expense));

        return redirect()->route('company.expenses.show', $expense)->with('success', 'Expense updated.');
    }

    public function show(Expense $expense)
    {
        $this->authorizeExpense($expense);
        $expense->load('head', 'items', 'company.currency', 'warrantyProvider');

        return view('company.expenses.show', compact('expense'));
    }

    public function destroy(Expense $expense)
    {
        $this->authorizeExpense($expense);
        $expense->delete();

        return redirect()->route('company.expenses.index')->with('success', 'Expense deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate(['expense_date' => ['required', 'date'], 'expense_head_id' => ['required', Rule::in($this->heads()->pluck('id')->all())], 'payment_method' => ['required', Rule::in($this->paymentMethods()->pluck('code')->all())], 'warranty_provider_id' => ['nullable', Rule::in($this->warrantyProviders()->pluck('id')->all())], 'warranty_duration_months' => ['nullable', 'integer', 'min:0', 'max:120'], 'remarks' => ['nullable', 'max:2000'], 'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'], 'items' => ['required', 'array', 'min:1'], 'items.*.description' => ['required', 'max:500'], 'items.*.quantity' => ['required', 'numeric', 'gt:0'], 'items.*.rate' => ['required', 'numeric', 'min:0'], 'items.*.tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100']]);
    }

    private function save(Request $request, array $data, ?Expense $expense = null): Expense
    {
        $subtotal = 0;
        $totalTax = 0;
        $items = [];
        foreach ($data['items'] as $row) {
            $base = round($row['quantity'] * $row['rate'], 2);
            $taxPercent = (float) ($row['tax_percent'] ?? 0);
            $taxAmount = round($base * $taxPercent / 100, 2);
            $subtotal += $base;
            $totalTax += $taxAmount;
            $items[] = ['description' => $row['description'], 'quantity' => $row['quantity'], 'rate' => $row['rate'], 'tax_percent' => $taxPercent, 'tax_amount' => $taxAmount, 'line_total' => round($base + $taxAmount, 2)];
        }
        $attachmentPath = $expense?->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('expense-attachments', 'public');
        }
        $branch = Branch::findOrFail($this->companyId());
        if (! $expense) {
            $seq = Expense::withTrashed()->where('branch_id', $branch->id)->count() + 1;
            $expense = new Expense(['invoice_no' => sprintf('%s-EXP-%06d', $branch->invoice_prefix ?: $branch->code, $seq), 'branch_id' => $branch->id, 'created_by' => auth()->id()]);
        }
        $provider = filled($data['warranty_provider_id'] ?? null) ? WarrantyProvider::find($data['warranty_provider_id']) : null;
        $expense->fill(['expense_head_id' => $data['expense_head_id'], 'expense_date' => $data['expense_date'], 'payment_method' => $data['payment_method'], 'warranty_provider_id' => $provider?->id, 'warranty_provider_name' => $provider?->name, 'warranty_duration_months' => $data['warranty_duration_months'] ?? null, 'remarks' => $data['remarks'] ?? null, 'attachment_path' => $attachmentPath, 'subtotal' => $subtotal, 'tax_amount' => $totalTax, 'total_amount' => round($subtotal + $totalTax, 2), 'status' => $expense->status ?: 'submitted']);
        $expense->save();
        $expense->items()->delete();
        $expense->items()->createMany($items);

        return $expense;
    }

    private function heads()
    {
        $company = ExpenseHead::where('branch_id', $this->companyId())->where('is_active', true)->orderBy('name')->get();

        return $company->isNotEmpty() ? $company : ExpenseHead::whereNull('branch_id')->where('is_active', true)->orderBy('name')->get();
    }

    private function paymentMethods()
    {
        $company = PaymentMethod::where('branch_id', $this->companyId())->where('is_active', true)->orderBy('name')->get();

        return $company->isNotEmpty() ? $company : PaymentMethod::whereNull('branch_id')->where('is_active', true)->orderBy('name')->get();
    }

    private function warrantyProviders()
    {
        $this->ensureWarrantyDefaults();

        return WarrantyProvider::where('branch_id', $this->companyId())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function ensureWarrantyDefaults(): void
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

    private function authorizeExpense(Expense $expense): void
    {
        abort_unless($expense->branch_id === $this->companyId(), 403);
    }
}
