<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyProvider extends Model
{
    protected $fillable = ['branch_id', 'name', 'is_system', 'is_active'];

    protected function casts(): array
    {
        return ['is_system' => 'boolean', 'is_active' => 'boolean'];
    }

    public function company()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
