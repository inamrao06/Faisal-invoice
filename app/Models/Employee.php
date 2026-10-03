<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'branch_id', 'employee_code', 'name', 'phone', 'email', 'cnic', 'designation',
        'department', 'address', 'joining_date', 'basic_salary', 'allowances', 'deductions',
        'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'deductions' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function salaries()
    {
        return $this->hasMany(EmployeeSalary::class);
    }

    /** Expected net pay per month based on the employee's fixed salary structure. */
    public function expectedNet(): float
    {
        return round((float) $this->basic_salary + (float) $this->allowances - (float) $this->deductions, 2);
    }
}
