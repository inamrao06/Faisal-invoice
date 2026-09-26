<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('business_records', function (Blueprint $table) {
            $table->id(); $table->string('module', 60)->index(); $table->string('record_no')->unique();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete(); $table->string('status')->default('draft')->index();
            $table->date('transaction_date')->nullable()->index(); $table->string('party_name')->nullable()->index(); $table->string('reference')->nullable()->index();
            $table->decimal('amount', 16, 2)->default(0); $table->decimal('paid_amount', 16, 2)->default(0); $table->decimal('balance_amount', 16, 2)->default(0);
            $table->json('payload')->nullable(); $table->json('attachments')->nullable(); $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); $table->timestamp('approved_at')->nullable();
            $table->timestamps(); $table->softDeletes(); $table->index(['module','branch_id','status']);
        });
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); $table->string('module'); $table->unsignedBigInteger('record_id')->nullable(); $table->json('old_values')->nullable();
            $table->json('new_values')->nullable(); $table->string('ip_address',45)->nullable(); $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('audit_logs'); Schema::dropIfExists('business_records'); }
};
