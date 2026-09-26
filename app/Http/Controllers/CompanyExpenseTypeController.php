<?php
namespace App\Http\Controllers;

use App\Models\ExpenseHead;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyExpenseTypeController extends Controller
{
    public function index(){return view('company.expense_types.index',['types'=>ExpenseHead::where('branch_id',$this->companyId())->latest()->paginate(15)]);}
    public function create(){return view('company.expense_types.form',['type'=>new ExpenseHead(['is_active'=>true])]);}
    public function store(Request $request){ExpenseHead::create($this->validated($request));return redirect()->route('company.expense-types.index')->with('success','Expense type created.');}
    public function edit(ExpenseHead $expense_type){$this->authorizeType($expense_type);return view('company.expense_types.form',['type'=>$expense_type]);}
    public function update(Request $request, ExpenseHead $expense_type){$this->authorizeType($expense_type);$expense_type->update($this->validated($request,$expense_type));return redirect()->route('company.expense-types.index')->with('success','Expense type updated.');}
    private function validated(Request $request, ?ExpenseHead $type=null): array {return $request->validate(['name'=>['required','max:120'],'code'=>['required','max:30',Rule::unique('expense_heads','code')->ignore($type)],'description'=>['nullable','max:1000'],'is_active'=>['nullable','boolean']])+['branch_id'=>$this->companyId(),'is_active'=>$request->boolean('is_active')];}
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeType(ExpenseHead $type): void { abort_unless($type->branch_id===$this->companyId(),403); }
}
