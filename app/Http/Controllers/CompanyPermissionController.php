<?php
namespace App\Http\Controllers;

use App\Models\CompanyPermission;
use App\Models\CompanyRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyPermissionController extends Controller
{
    public function index(){return view('company.permissions.index',['permissions'=>CompanyPermission::with('role')->where('branch_id',$this->companyId())->latest()->paginate(15)]);}
    public function create(){return view('company.permissions.form',['permission'=>new CompanyPermission(['can_view'=>true]),'roles'=>$this->roles()]);}
    public function store(Request $request){CompanyPermission::create($this->validated($request));return redirect()->route('company.permissions.index')->with('success','Permission created.');}
    public function edit(CompanyPermission $permission){$this->authorizePermission($permission);return view('company.permissions.form',['permission'=>$permission,'roles'=>$this->roles()]);}
    public function update(Request $request, CompanyPermission $permission){$this->authorizePermission($permission);$permission->update($this->validated($request,$permission));return redirect()->route('company.permissions.index')->with('success','Permission updated.');}
    private function validated(Request $request, ?CompanyPermission $permission=null): array {return $request->validate(['module_name'=>['required','max:120'],'company_role_id'=>['required',Rule::in($this->roles()->pluck('id')->all())],'can_create'=>['nullable','boolean'],'can_update'=>['nullable','boolean'],'can_view'=>['nullable','boolean'],'can_delete'=>['nullable','boolean']])+['branch_id'=>$this->companyId(),'can_create'=>$request->boolean('can_create'),'can_update'=>$request->boolean('can_update'),'can_view'=>$request->boolean('can_view'),'can_delete'=>$request->boolean('can_delete')];}
    private function roles(){return CompanyRole::where('branch_id',$this->companyId())->where('is_active',true)->orderBy('name')->get();}
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizePermission(CompanyPermission $permission): void { abort_unless($permission->branch_id===$this->companyId(),403); }
}
