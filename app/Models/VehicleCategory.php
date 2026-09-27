<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCategory extends Model
{
    protected $fillable = ['branch_id','name','code','classification_text','terms','default_warranty_provider_id','default_warranty_months','is_active'];
    protected function casts(): array { return ['is_active'=>'boolean']; }
    public function warrantyProvider(){return $this->belongsTo(WarrantyProvider::class,'default_warranty_provider_id');}
}
