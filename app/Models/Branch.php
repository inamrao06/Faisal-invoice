<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        // Identity
        'code', 'name', 'type', 'currency_id',
        // Contact
        'phone', 'email', 'company_email', 'website',
        // Location
        'address', 'city',
        // People
        'manager_name', 'authorized_person', 'designation',
        // Files
        'logo_path', 'signature_path', 'stamp_path',
        // Business
        'invoice_prefix', 'tax_number',
        // Status
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('user_type', 'admin');
    }

    public function records()
    {
        return $this->hasMany(BusinessRecord::class);
    }
}
