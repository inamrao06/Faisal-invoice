<?php
namespace App\Http\Controllers;

use App\Models\CompanyPermission;
use App\Models\CompanyRole;
use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\User;
use App\Models\Branch;

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
