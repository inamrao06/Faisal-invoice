<?php
namespace App\Http\Controllers;

use App\Models\VehicleCategory;
use App\Models\WarrantyProvider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyVehicleCategoryController extends Controller
{
    public function index(){ $this->ensureDefaults(); return view('company.vehicle_categories.index',['categories'=>VehicleCategory::where('branch_id',$this->companyId())->latest()->paginate(15)]); }
    public function create(){ return view('company.vehicle_categories.form',['category'=>new VehicleCategory(['is_active'=>true]),'providers'=>$this->providers()]); }
    public function store(Request $request){ VehicleCategory::create($this->validated($request)); return redirect()->route('company.vehicle-categories.index')->with('success','Vehicle category created.'); }
    public function edit(VehicleCategory $vehicle_category){ $this->authorizeCategory($vehicle_category); return view('company.vehicle_categories.form',['category'=>$vehicle_category,'providers'=>$this->providers()]); }
    public function update(Request $request, VehicleCategory $vehicle_category){ $this->authorizeCategory($vehicle_category); $vehicle_category->update($this->validated($request,$vehicle_category)); return redirect()->route('company.vehicle-categories.index')->with('success','Vehicle category updated.'); }
    private function validated(Request $request, ?VehicleCategory $category=null): array { return $request->validate(['name'=>['required','max:120'],'code'=>['required','max:60',Rule::unique('vehicle_categories','code')->where('branch_id',$this->companyId())->ignore($category)],'classification_text'=>['nullable','max:2000'],'terms'=>['nullable','max:10000'],'default_warranty_provider_id'=>['nullable',Rule::in($this->providers()->pluck('id')->all())],'default_warranty_months'=>['nullable','integer','min:0','max:120'],'is_active'=>['nullable','boolean']])+['branch_id'=>$this->companyId(),'is_active'=>$request->boolean('is_active')]; }
    private function providers(){ return WarrantyProvider::where('branch_id',$this->companyId())->where('is_active',true)->orderBy('name')->get(); }
    private function ensureDefaults(): void { $handler = WarrantyProvider::firstOrCreate(['branch_id'=>$this->companyId(),'name'=>'Handler Protect'],['is_system'=>true,'is_active'=>true]); foreach ([['Japanese Import Used Cars (JDM)','jdm','Japanese Import Used (JDM) Vehicle','This invoice identifies the vehicle as a Japanese Import Used (JDM) Vehicle. Relevant JDM warranty and sale terms apply.',18,$handler->id],['Local Used Cars','local_used','Local Used Vehicle','This invoice identifies the vehicle as a Local Used Vehicle. Relevant local-used-car warranty and sale terms apply.',3,$handler->id],['Part Exchange Stock Clearance Cars','part_exchange_clearance','PART EXCHANGE STOCK CLEARANCE VEHICLE','This vehicle is sold as a PART EXCHANGE STOCK CLEARANCE VEHICLE and is sold AS SPARES AND REPAIRS, AS INSPECTED, and in its current condition. The vehicle is offered at a reduced stock-clearance price and has not been prepared or reconditioned to normal retail-sale standard. The buyer accepts responsibility for all repairs, servicing, maintenance, replacement parts, MOT work and all costs after purchase.',null,null]] as $row) { VehicleCategory::firstOrCreate(['branch_id'=>$this->companyId(),'code'=>$row[1]],['name'=>$row[0],'classification_text'=>$row[2],'terms'=>$row[3],'default_warranty_months'=>$row[4],'default_warranty_provider_id'=>$row[5],'is_active'=>true]); } }
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeCategory(VehicleCategory $category): void { abort_unless($category->branch_id===$this->companyId(),403); }
}
