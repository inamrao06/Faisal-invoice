<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyDuration extends Model
{
    protected $fillable = ['branch_id', 'name', 'months', 'is_system', 'is_active'];

    protected function casts(): array
    {
        return ['months' => 'integer', 'is_system' => 'boolean', 'is_active' => 'boolean'];
    }
}
