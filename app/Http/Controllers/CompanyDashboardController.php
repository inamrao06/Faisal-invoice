<?php
namespace App\Http\Controllers;

use App\Models\CompanyPermission;
use App\Models\CompanyRole;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\Branch;
use App\Models\User;
use App\Models\VehicleSaleInvoice;

class CompanyDashboardController extends Controller
{
    public function __invoke()
    {
        $companyId = auth()->user()->branch_id;
        abort_unless($companyId, 403);
        $company = Branch::with('currency')->findOrFail($companyId);
        $cards = [
            ['label'=>'Company Users','value'=>User::where('branch_id',$companyId)->count(),'icon'=>'people','tone'=>'primary'],
            ['label'=>'Expense Types','value'=>ExpenseHead::where('branch_id',$companyId)->count(),'icon'=>'tags','tone'=>'success'],
            ['label'=>'Roles','value'=>CompanyRole::where('branch_id',$companyId)->count(),'icon'=>'person-badge','tone'=>'warning'],
            ['label'=>'Total Expenses','value'=>Expense::where('branch_id',$companyId)->sum('total_amount'),'icon'=>'cash-coin','tone'=>'danger','money'=>true],
            ['label'=>'Total Invoices','value'=>VehicleSaleInvoice::where('branch_id',$companyId)->sum('total_sale_price'),'icon'=>'invoice','tone'=>'info','money'=>true],
            ['label'=>'Total Salaries','value'=>EmployeeSalary::where('branch_id',$companyId)->sum('net_salary'),'icon'=>'salary','tone'=>'secondary','money'=>true],
        ];
        $recentExpenses = Expense::with('head')->where('branch_id',$companyId)->latest('expense_date')->limit(8)->get();
        $recentUsers = User::where('branch_id',$companyId)->latest()->limit(5)->get();
        $roles = CompanyRole::where('branch_id',$companyId)->latest()->limit(5)->get();
        $expenseTypes = ExpenseHead::where('branch_id',$companyId)->latest()->limit(5)->get();
        $permissionCount = CompanyPermission::where('branch_id',$companyId)->count();
        $monthlyExpense = Expense::where('branch_id',$companyId)->whereDate('expense_date','>=',now()->startOfMonth())->sum('total_amount');
        $financeChart = collect(range(5, 0))->map(function ($monthsAgo) use ($companyId) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);
            $nextMonth = (clone $month)->addMonth();

            return [
                'label' => $month->format('M'),
                'expenses' => (float) Expense::where('branch_id', $companyId)
                    ->where('expense_date', '>=', $month)
                    ->where('expense_date', '<', $nextMonth)
                    ->sum('total_amount'),
                'invoices' => (float) VehicleSaleInvoice::where('branch_id', $companyId)
                    ->where('invoice_date', '>=', $month)
                    ->where('invoice_date', '<', $nextMonth)
                    ->sum('total_sale_price'),
                'salaries' => (float) EmployeeSalary::where('branch_id', $companyId)
                    ->where('month', '>=', $month)
                    ->where('month', '<', $nextMonth)
                    ->sum('net_salary'),
            ];
        });
        $chartMax = max(1, $financeChart->flatMap(fn ($month) => [$month['expenses'], $month['invoices'], $month['salaries']])->max());

        return view('company.dashboard', compact('company','cards','recentExpenses','recentUsers','roles','expenseTypes','permissionCount','monthlyExpense','financeChart','chartMax'));
    }
}
