<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessRecord extends Model
{
    use SoftDeletes;

    protected $fillable = ['module', 'record_no', 'branch_id', 'status', 'transaction_date', 'party_name', 'reference', 'amount', 'paid_amount', 'balance_amount', 'payload', 'attachments', 'created_by', 'approved_by', 'approved_at'];

    protected function casts(): array
    {
        return ['transaction_date' => 'date', 'payload' => 'array', 'attachments' => 'array', 'approved_at' => 'datetime'];
    }

    public function company()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
