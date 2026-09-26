<?php
namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\ExpenseHead;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompanyExpenseController extends Controller
{
    public function index(){return view('company.expenses.index',['expenses'=>Expense::with('head')->where('branch_id',$this->companyId())->latest('expense_date')->paginate(15)]);}
    public function create(){return view('company.expenses.form',['expense'=>new Expense(['expense_date'=>now(),'status'=>'submitted']),'heads'=>$this->heads(),'paymentMethods'=>$this->paymentMethods()]);}
    public function store(Request $request){$expense=DB::transaction(fn()=> $this->save($request,$this->validated($request)));return redirect()->route('company.expenses.show',$expense)->with('success','Expense created.');}
    public function show(Expense $expense){$this->authorizeExpense($expense);$expense->load('head','items');return view('company.expenses.show',compact('expense'));}
    private function validated(Request $request): array {return $request->validate(['expense_date'=>['required','date'],'expense_head_id'=>['required',Rule::in($this->heads()->pluck('id')->all())],'payment_method'=>['required',Rule::in($this->paymentMethods()->pluck('code')->all())],'remarks'=>['nullable','max:2000'],'attachment'=>['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:5120'],'items'=>['required','array','min:1'],'items.*.description'=>['required','max:500'],'items.*.quantity'=>['required','numeric','gt:0'],'items.*.rate'=>['required','numeric','min:0'],'items.*.tax_percent'=>['nullable','numeric','min:0','max:100']]);}
    private function save(Request $request,array $data): Expense {$subtotal=0;$totalTax=0;$items=[];foreach($data['items'] as $row){$base=round($row['quantity']*$row['rate'],2);$taxPercent=(float)($row['tax_percent']??0);$taxAmount=round($base*$taxPercent/100,2);$subtotal+=$base;$totalTax+=$taxAmount;$items[]=['description'=>$row['description'],'quantity'=>$row['quantity'],'rate'=>$row['rate'],'tax_percent'=>$taxPercent,'tax_amount'=>$taxAmount,'line_total'=>round($base+$taxAmount,2)];}$attachmentPath=$request->hasFile('attachment')?$request->file('attachment')->store('expense-attachments','public'):null;$branch=Branch::findOrFail($this->companyId());$seq=Expense::withTrashed()->where('branch_id',$branch->id)->count()+1;$expense=Expense::create(['invoice_no'=>sprintf('%s-EXP-%06d',$branch->invoice_prefix?:$branch->code,$seq),'branch_id'=>$branch->id,'expense_head_id'=>$data['expense_head_id'],'expense_date'=>$data['expense_date'],'payment_method'=>$data['payment_method'],'remarks'=>$data['remarks']??null,'attachment_path'=>$attachmentPath,'subtotal'=>$subtotal,'tax_amount'=>$totalTax,'total_amount'=>round($subtotal+$totalTax,2),'status'=>'submitted','created_by'=>auth()->id()]);$expense->items()->createMany($items);return $expense;}
    private function heads(){$company=ExpenseHead::where('branch_id',$this->companyId())->where('is_active',true)->orderBy('name')->get();return $company->isNotEmpty()?$company:ExpenseHead::whereNull('branch_id')->where('is_active',true)->orderBy('name')->get();}
    private function paymentMethods(){$company=PaymentMethod::where('branch_id',$this->companyId())->where('is_active',true)->orderBy('name')->get();return $company->isNotEmpty()?$company:PaymentMethod::whereNull('branch_id')->where('is_active',true)->orderBy('name')->get();}
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeExpense(Expense $expense): void { abort_unless($expense->branch_id===$this->companyId(),403); }
}
