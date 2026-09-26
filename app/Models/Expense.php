<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;
    protected $fillable = ['invoice_no','branch_id','expense_head_id','expense_date','payment_method','warranty_provider_id','warranty_provider_name','warranty_duration_months','remarks','attachment_path','subtotal','tax_amount','total_amount','status','created_by'];
    protected function casts(): array { return ['expense_date'=>'date','subtotal'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2']; }
    public function head(){return $this->belongsTo(ExpenseHead::class,'expense_head_id');}
    public function company(){return $this->belongsTo(Branch::class,'branch_id');}
    public function items(){return $this->hasMany(ExpenseItem::class);}
    public function warrantyProvider(){return $this->belongsTo(WarrantyProvider::class,'warranty_provider_id');}
}
