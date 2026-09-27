<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleInvoicePayment extends Model
{
    protected $fillable = ['vehicle_sale_invoice_id','payment_date','payment_type','method','amount','notes'];
    protected function casts(): array { return ['payment_date'=>'date','amount'=>'decimal:2']; }
}
