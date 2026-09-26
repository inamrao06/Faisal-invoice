<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseHead;
use Illuminate\Http\Request;

class CompanyExpenseReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $companyId = auth()->user()->branch_id;
        abort_unless($companyId,403);
        $query = Expense::with('head')->where('branch_id',$companyId)
            ->when($request->from,fn($q,$v)=>$q->whereDate('expense_date','>=',$v))
            ->when($request->to,fn($q,$v)=>$q->whereDate('expense_date','<=',$v))
            ->when($request->expense_head_id,fn($q,$v)=>$q->where('expense_head_id',$v));
        $summary = (clone $query)->selectRaw('count(*) records, coalesce(sum(total_amount),0) total')->first();
        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();
        $heads = ExpenseHead::where('branch_id',$companyId)->orderBy('name')->get();
        if ($heads->isEmpty()) $heads = ExpenseHead::whereNull('branch_id')->orderBy('name')->get();
        return view('company.reports.expenses',compact('expenses','summary','heads'));
    }
}
