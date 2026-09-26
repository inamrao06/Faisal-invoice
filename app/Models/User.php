<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** user_type values */
    public const USER_TYPES = ['super_admin', 'admin'];

    /** Kept for backward-compat */
    public const ROLES = ['super_admin', 'admin'];

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'role',             // legacy enum column
        'user_type',        // super_admin | admin
        'company_role_num', // 0 = full access; >0 = restricted
        'branch_id',
        'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'is_active'          => 'boolean',
            'company_role_num'   => 'integer',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────

    public function company()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function branch()
    {
        return $this->company();
    }

    // ── Role helpers ───────────────────────────────────────────────────

    /** Global super-admin — no company restriction. */
    public function isSuperUser(): bool
    {
        return $this->user_type === 'super_admin'
            || $this->role === 'super_admin';
    }

    /** Alias for gate definitions. */
    public function isGlobalAdmin(): bool
    {
        return $this->isSuperUser();
    }

    /**
     * Full access = super_admin OR admin with company_role_num === 0.
     * Role 0 means the admin has unrestricted access to their company.
     */
    public function hasFullBranchAccess(): bool
    {
        if ($this->isSuperUser()) return true;
        return $this->user_type === 'admin' && (int) $this->company_role_num === 0;
    }

    /**
     * Any company-scoped admin (all access levels).
     * Used by the 'company-area' gate.
     */
    public function isCompanyAdmin(): bool
    {
        return $this->user_type === 'admin' && filled($this->branch_id);
    }

    /** Human-readable access label. */
    public function accessLabel(): string
    {
        if ($this->isSuperUser())        return 'Super Admin';
        if ((int) $this->company_role_num === 0) return 'Full Access';
        return 'Role ' . $this->company_role_num;
    }
}
