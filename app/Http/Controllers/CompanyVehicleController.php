<?php
namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleCategory;
use App\Models\CarSpecification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyVehicleController extends Controller
{
    public function index(Request $request){ return view('company.vehicles.index',['vehicles'=>Vehicle::with('category','condition','brand','modelSpec','fuelType','transmissionType')->where('branch_id',$this->companyId())->when($request->q,fn($q,$v)=>$q->where(fn($x)=>$x->where('make_model','like',"%$v%")->orWhere('registration_no','like',"%$v%")->orWhere('vin','like',"%$v%")))->latest()->paginate(15)->withQueryString()]); }
    public function create(){ return view('company.vehicles.form',$this->formData(new Vehicle(['status'=>'available']))); }
    public function store(Request $request){ Vehicle::create($this->validated($request)); return redirect()->route('company.vehicles.index')->with('success','Vehicle saved.'); }
    public function edit(Vehicle $vehicle){ $this->authorizeVehicle($vehicle); return view('company.vehicles.form',$this->formData($vehicle)); }
    public function update(Request $request, Vehicle $vehicle){ $this->authorizeVehicle($vehicle); $vehicle->update($this->validated($request)); return redirect()->route('company.vehicles.index')->with('success','Vehicle updated.'); }
    private function validated(Request $request): array { return $request->validate(['vehicle_category_id'=>['nullable',Rule::in($this->categories()->pluck('id')->all())],'condition_id'=>['nullable',Rule::in($this->specs('condition')->pluck('id')->all())],'brand_id'=>['nullable',Rule::in($this->specs('brand')->pluck('id')->all())],'model_id'=>['nullable',Rule::in($this->specs('model')->pluck('id')->all())],'fuel_type_id'=>['nullable',Rule::in($this->specs('fuel_type')->pluck('id')->all())],'transmission_type_id'=>['nullable',Rule::in($this->specs('transmission_type')->pluck('id')->all())],'make_model'=>['required','max:150'],'registration_no'=>['nullable','max:40'],'vin'=>['nullable','max:80'],'year'=>['nullable','integer','min:1900','max:2100'],'mileage'=>['nullable','integer','min:0'],'keys_count'=>['nullable','integer','min:0','max:20'],'sale_price'=>['nullable','numeric','min:0'],'status'=>['required',Rule::in(['available','reserved','sold'])]])+['branch_id'=>$this->companyId()]; }
    private function formData(Vehicle $vehicle): array { return ['vehicle'=>$vehicle,'categories'=>$this->categories(),'conditions'=>$this->specs('condition'),'brands'=>$this->specs('brand'),'models'=>$this->specs('model'),'fuelTypes'=>$this->specs('fuel_type'),'transmissionTypes'=>$this->specs('transmission_type')]; }
    private function categories(){ return VehicleCategory::where('branch_id',$this->companyId())->where('is_active',true)->orderBy('name')->get(); }
    private function specs(string $type){ return CarSpecification::where('branch_id',$this->companyId())->where('type',$type)->where('is_active',true)->orderBy('name')->get(); }
    private function companyId(): int { abort_unless(auth()->user()->branch_id,403); return auth()->user()->branch_id; }
    private function authorizeVehicle(Vehicle $vehicle): void { abort_unless($vehicle->branch_id===$this->companyId(),403); }
}
