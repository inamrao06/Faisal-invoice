<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPermission extends Model
{
    protected $fillable = ['branch_id', 'company_role_id', 'module_name', 'can_create', 'can_update', 'can_view', 'can_delete'];
    protected function casts(): array { return ['can_create'=>'boolean','can_update'=>'boolean','can_view'=>'boolean','can_delete'=>'boolean']; }
    public function role() { return $this->belongsTo(CompanyRole::class, 'company_role_id'); }
}
