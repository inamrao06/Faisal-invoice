<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['branch_id','vehicle_category_id','condition_id','brand_id','model_id','fuel_type_id','transmission_type_id','make_model','registration_no','vin','year','mileage','keys_count','sale_price','status'];
    protected function casts(): array { return ['sale_price'=>'decimal:2']; }
    public function category(){return $this->belongsTo(VehicleCategory::class,'vehicle_category_id');}
    public function condition(){return $this->belongsTo(CarSpecification::class,'condition_id');}
    public function brand(){return $this->belongsTo(CarSpecification::class,'brand_id');}
    public function modelSpec(){return $this->belongsTo(CarSpecification::class,'model_id');}
    public function fuelType(){return $this->belongsTo(CarSpecification::class,'fuel_type_id');}
    public function transmissionType(){return $this->belongsTo(CarSpecification::class,'transmission_type_id');}
    public function expenses(){return $this->hasMany(Expense::class);}
}
