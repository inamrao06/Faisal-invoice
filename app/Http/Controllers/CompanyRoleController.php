<?php
namespace App\Http\Controllers;

use App\Models\CompanyRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyRoleController extends Controller
{
    public function index(){return view('company.roles.index',['roles'=>CompanyRole::where('branch_id',$this->companyId())->latest()->paginate(15)]);}
    public function create(){return view('company.roles.form',['role'=>new CompanyRole(['is_active'=>true])]);}
    public function store(Request $request){CompanyRole::create($this->validated($request));return redirect()->route('company.roles.index')->with('success','Role created.');}
    public function edit(CompanyRole $role){$this->authorizeRole($role);return view('company.roles.form',compact('role'));}
    public function update(Request $request, CompanyRole $role){$this->authorizeRole($role);$role->update($this->validated($request,$role));return redirect()->route('company.roles.index')->with('success','Role updated.');}
    private function validated(Request $request, ?CompanyRole $role=null): array {return $request->validate(['name'=>['required','max:120',Rule::unique('company_roles','name')->where('branch_id',$this->companyId())->ignore($role)],'description'=>['nullable','max:1000'],'is_active'=>['nullable','boolean']])+['branch_id'=>$this->companyId(),'is_active'=>$request->boolean('is_active')];}
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeRole(CompanyRole $role): void { abort_unless($role->branch_id===$this->companyId(),403); }
}
