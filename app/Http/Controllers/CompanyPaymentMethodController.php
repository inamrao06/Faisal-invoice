<?php
namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyPaymentMethodController extends Controller
{
    public function index(){return view('company.payment_methods.index',['methods'=>PaymentMethod::where('branch_id',$this->companyId())->latest()->paginate(15)]);}
    public function create(){return view('company.payment_methods.form',['method'=>new PaymentMethod(['is_active'=>true])]);}
    public function store(Request $request){PaymentMethod::create($this->validated($request));return redirect()->route('company.payment-methods.index')->with('success','Payment method created.');}
    public function edit(PaymentMethod $payment_method){$this->authorizeMethod($payment_method);return view('company.payment_methods.form',['method'=>$payment_method]);}
    public function update(Request $request, PaymentMethod $payment_method){$this->authorizeMethod($payment_method);$payment_method->update($this->validated($request,$payment_method));return redirect()->route('company.payment-methods.index')->with('success','Payment method updated.');}
    private function validated(Request $request, ?PaymentMethod $method=null): array {return $request->validate(['name'=>['required','max:120'],'code'=>['required','max:40',Rule::unique('payment_methods','code')->ignore($method)],'description'=>['nullable','max:1000'],'is_active'=>['nullable','boolean']])+['branch_id'=>$this->companyId(),'is_active'=>$request->boolean('is_active')];}
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeMethod(PaymentMethod $method): void { abort_unless($method->branch_id===$this->companyId(),403); }
}
