<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleSaleInvoice extends Model
{
    use SoftDeletes;
    protected $fillable = ['invoice_no','branch_id','customer_id','seller_customer_id','vehicle_id','vehicle_category_id','invoice_date','sale_date','transaction_type','vehicle_price','discount','commission_amount','commission_notes','total_sale_price','total_paid','balance_amount','payment_status','warranty_provider_id','warranty_provider_name','warranty_duration_months','warranty_terms','vehicle_terms','general_terms','notes','created_by'];
    protected function casts(): array { return ['invoice_date'=>'date','sale_date'=>'date','vehicle_price'=>'decimal:2','discount'=>'decimal:2','commission_amount'=>'decimal:2','total_sale_price'=>'decimal:2','total_paid'=>'decimal:2','balance_amount'=>'decimal:2']; }
    public function customer(){return $this->belongsTo(Customer::class);}
    public function sellerCustomer(){return $this->belongsTo(Customer::class,'seller_customer_id');}
    public function company(){return $this->belongsTo(Branch::class,'branch_id');}
    public function vehicle(){return $this->belongsTo(Vehicle::class);}
    public function category(){return $this->belongsTo(VehicleCategory::class,'vehicle_category_id');}
    public function warrantyProvider(){return $this->belongsTo(WarrantyProvider::class,'warranty_provider_id');}
    public function payments(){return $this->hasMany(VehicleInvoicePayment::class);}
}
