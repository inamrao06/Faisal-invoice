<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyRole extends Model
{
    protected $fillable = ['branch_id', 'name', 'description', 'is_active'];
    protected function casts(): array { return ['is_active' => 'boolean']; }
    public function permissions() { return $this->hasMany(CompanyPermission::class); }
}
