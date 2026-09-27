<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarSpecification extends Model
{
    public const TYPES = [
        'condition' => 'Conditions',
        'brand' => 'Brands',
        'model' => 'Models',
        'fuel_type' => 'Fuel Types',
        'transmission_type' => 'Transmission Types',
    ];

    protected $fillable = ['branch_id', 'type', 'name', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
