<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchType;
use App\Models\BusinessRecord;
use App\Models\Currency;
use App\Models\CompanyPermission;
use App\Models\CompanyRole;
use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $companyId = $user->isSuperUser() ? null : $user->branch_id;

        $companies = Branch::query()->when($companyId, fn($q) => $q->whereKey($companyId));
        $users = User::query()->when($companyId, fn($q) => $q->where('branch_id', $companyId));
        $records = BusinessRecord::query()->when($companyId, fn($q) => $q->where('branch_id', $companyId));

        $cards = [
            ['label' => 'Companies', 'value' => (clone $companies)->count(), 'icon' => 'buildings', 'tone' => 'primary'],
            ['label' => 'Active Companies', 'value' => (clone $companies)->where('is_active', true)->count(), 'icon' => 'check2-circle', 'tone' => 'success'],
            ['label' => 'Admins', 'value' => (clone $users)->where('role', 'admin')->count(), 'icon' => 'person-badge', 'tone' => 'warning'],
            ['label' => 'Total Business', 'value' => (clone $records)->sum('amount'), 'icon' => 'graph-up-arrow', 'tone' => 'danger', 'money' => true],
        ];

        $companyTypes = BranchType::orderBy('name')->get();
        $recentCompanies = (clone $companies)->with('currency')->latest()->limit(8)->get();
        $currencies = Currency::where('is_active', true)->count();

        if ($user->isSuperUser()) {
            return view('dashboards.super_admin', compact('cards', 'companyTypes', 'recentCompanies', 'currencies'));
        }

        $company = Branch::with('currency')->findOrFail($companyId);
        $cards = [
            ['label'=>'Company Users','value'=>User::where('branch_id',$companyId)->count(),'icon'=>'people','tone'=>'primary'],
            ['label'=>'Expense Types','value'=>ExpenseHead::where('branch_id',$companyId)->count(),'icon'=>'tags','tone'=>'success'],
            ['label'=>'Roles','value'=>CompanyRole::where('branch_id',$companyId)->count(),'icon'=>'person-badge','tone'=>'warning'],
            ['label'=>'Total Expenses','value'=>Expense::where('branch_id',$companyId)->sum('total_amount'),'icon'=>'cash-coin','tone'=>'danger','money'=>true],
        ];
        $recentExpenses = Expense::with('head')->where('branch_id',$companyId)->latest('expense_date')->limit(8)->get();
        $recentUsers = User::where('branch_id',$companyId)->latest()->limit(5)->get();
        $roles = CompanyRole::where('branch_id',$companyId)->latest()->limit(5)->get();
        $expenseTypes = ExpenseHead::where('branch_id',$companyId)->latest()->limit(5)->get();
        $permissionCount = CompanyPermission::where('branch_id',$companyId)->count();
        $monthlyExpense = Expense::where('branch_id',$companyId)->whereDate('expense_date','>=',now()->startOfMonth())->sum('total_amount');
        return view('company.dashboard', compact('company','cards','recentExpenses','recentUsers','roles','expenseTypes','permissionCount','monthlyExpense'));
    }
}
