<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'branch_id', 'employee_id', 'month', 'basic_salary', 'allowances', 'bonus', 'overtime',
        'deductions', 'advance', 'net_salary', 'status', 'payment_method', 'paid_date', 'notes',
        'generated_by',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'paid_date' => 'date',
            'basic_salary' => 'decimal:2',
            'allowances' => 'decimal:2',
            'bonus' => 'decimal:2',
            'overtime' => 'decimal:2',
            'deductions' => 'decimal:2',
            'advance' => 'decimal:2',
            'net_salary' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::saving(fn (self $salary) => $salary->net_salary = $salary->netOf());
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function company()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function netOf(): float
    {
        return round(
            (float) $this->basic_salary + (float) $this->allowances + (float) $this->bonus + (float) $this->overtime
            - (float) $this->deductions - (float) $this->advance,
            2
        );
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function monthLabel(): string
    {
        return $this->month?->format('F Y') ?? '';
    }
}
