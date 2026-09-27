<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CompanyCustomerController extends Controller
{
    public function index(Request $request){ return view('company.customers.index',['customers'=>Customer::where('branch_id',$this->companyId())->when($request->q,fn($q,$v)=>$q->where(fn($x)=>$x->where('name','like',"%$v%")->orWhere('email','like',"%$v%")->orWhere('phone','like',"%$v%")))->latest()->paginate(15)->withQueryString()]); }
    public function create(){ return view('company.customers.form',['customer'=>new Customer]); }
    public function store(Request $request){ Customer::create($this->validated($request)); return redirect()->route('company.customers.index')->with('success','Customer saved.'); }
    public function edit(Customer $customer){ $this->authorizeCustomer($customer); return view('company.customers.form',compact('customer')); }
    public function update(Request $request, Customer $customer){ $this->authorizeCustomer($customer); $customer->update($this->validated($request)); return redirect()->route('company.customers.index')->with('success','Customer updated.'); }
    private function validated(Request $request): array { return $request->validate(['name'=>['required','max:150'],'email'=>['nullable','email','max:150'],'phone'=>['nullable','max:50'],'address'=>['nullable','max:1000'],'postcode'=>['nullable','max:30']])+['branch_id'=>$this->companyId()]; }
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeCustomer(Customer $customer): void { abort_unless($customer->branch_id===$this->companyId(),403); }
}
