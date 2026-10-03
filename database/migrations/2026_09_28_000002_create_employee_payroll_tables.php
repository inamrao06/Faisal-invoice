<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('employee_code', 40);
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('cnic', 30)->nullable();
            $table->string('designation', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('address')->nullable();
            $table->date('joining_date')->nullable();
            $table->decimal('basic_salary', 16, 2)->default(0);
            $table->decimal('allowances', 16, 2)->default(0);
            $table->decimal('deductions', 16, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'employee_code']);
            $table->index(['branch_id', 'is_active']);
        });

        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('month');
            $table->decimal('basic_salary', 16, 2)->default(0);
            $table->decimal('allowances', 16, 2)->default(0);
            $table->decimal('bonus', 16, 2)->default(0);
            $table->decimal('overtime', 16, 2)->default(0);
            $table->decimal('deductions', 16, 2)->default(0);
            $table->decimal('advance', 16, 2)->default(0);
            $table->decimal('net_salary', 16, 2)->default(0);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->string('payment_method', 30)->nullable();
            $table->date('paid_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['branch_id', 'employee_id', 'month']);
            $table->index(['branch_id', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
        Schema::dropIfExists('employees');
    }
};
