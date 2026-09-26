<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('branches', function (Blueprint $table) {
            $table->id(); $table->string('code')->unique(); $table->string('name');
            $table->enum('type', ['car_sale_purchase','service_booking','pos']);
            $table->text('address')->nullable(); $table->string('city')->nullable(); $table->string('phone')->nullable();
            $table->string('email')->nullable(); $table->string('manager_name')->nullable(); $table->string('logo_path')->nullable();
            $table->string('invoice_prefix', 20)->nullable(); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id(); $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); $table->string('name');
            $table->string('email')->unique(); $table->string('phone', 30)->nullable(); $table->timestamp('email_verified_at')->nullable(); $table->string('password');
            $table->enum('role', ['super_admin','admin','owner','branch_admin','branch_manager','sales','purchase','service_advisor','technician','cashier','store','hr','accountant','viewer'])->default('viewer');
            $table->boolean('is_active')->default(true); $table->rememberToken(); $table->timestamps();
        });
        Schema::create('password_reset_tokens', function (Blueprint $table) { $table->string('email')->primary(); $table->string('token'); $table->timestamp('created_at')->nullable(); });
        Schema::create('sessions', function (Blueprint $table) { $table->string('id')->primary(); $table->foreignId('user_id')->nullable()->index(); $table->string('ip_address',45)->nullable(); $table->text('user_agent')->nullable(); $table->longText('payload'); $table->integer('last_activity')->index(); });
        Schema::create('cache', function (Blueprint $table) { $table->string('key')->primary(); $table->mediumText('value'); $table->integer('expiration'); });
        Schema::create('cache_locks', function (Blueprint $table) { $table->string('key')->primary(); $table->string('owner'); $table->integer('expiration'); });
    }
    public function down(): void { Schema::dropIfExists('cache_locks'); Schema::dropIfExists('cache'); Schema::dropIfExists('sessions'); Schema::dropIfExists('password_reset_tokens'); Schema::dropIfExists('users'); Schema::dropIfExists('branches'); }
};
