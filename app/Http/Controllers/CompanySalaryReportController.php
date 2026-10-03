<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanySalaryReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $companyId = auth()->user()->branch_id;
        abort_unless($companyId, 403);

        $from = $request->from ? Carbon::createFromFormat('Y-m', substr($request->from, 0, 7))->startOfMonth() : null;
        $to = $request->to ? Carbon::createFromFormat('Y-m', substr($request->to, 0, 7))->startOfMonth() : null;

        $query = EmployeeSalary::with('employee')->where('branch_id', $companyId)
            ->when($from, fn ($q) => $q->whereDate('month', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('month', '<=', $to))
            ->when($request->employee_id, fn ($q, $v) => $q->where('employee_id', $v))
            ->when(in_array($request->status, ['pending', 'paid'], true), fn ($q) => $q->where('status', $request->status));

        $totals = (clone $query)->selectRaw('count(*) records,
                coalesce(sum(basic_salary),0) basic, coalesce(sum(allowances),0) allowances,
                coalesce(sum(bonus),0) bonus, coalesce(sum(overtime),0) overtime,
                coalesce(sum(deductions),0) deductions, coalesce(sum(advance),0) advance,
                coalesce(sum(net_salary),0) net,
                coalesce(sum(case when status = "paid" then net_salary else 0 end),0) paid,
                coalesce(sum(case when status = "pending" then net_salary else 0 end),0) pending')
            ->first();

        $byMonth = (clone $query)
            ->selectRaw('month, count(*) employees, coalesce(sum(basic_salary + allowances + bonus + overtime),0) earnings,
                         coalesce(sum(deductions + advance),0) deductions, coalesce(sum(net_salary),0) net,
                         coalesce(sum(case when status = "paid" then net_salary else 0 end),0) paid,
                         coalesce(sum(case when status = "pending" then net_salary else 0 end),0) pending')
            ->groupBy('month')
            ->orderByDesc('month')
            ->get();

        return view('company.reports.salaries', [
            'salaries' => $query->latest('month')->orderBy('id')->paginate(20)->withQueryString(),
            'totals' => $totals,
            'byMonth' => $byMonth,
            'employees' => Employee::where('branch_id', $companyId)->orderBy('name')->pluck('name', 'id'),
            'from' => $request->from,
            'to' => $request->to,
            'symbol' => auth()->user()->company?->currency?->symbol ?: 'PKR',
        ]);
    }
}
