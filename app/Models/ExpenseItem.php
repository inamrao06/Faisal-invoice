<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $fillable = ['description','quantity','rate','tax_percent','tax_amount','line_total'];
}
