<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('expense_heads', function (Blueprint $table) {
            if (!Schema::hasColumn('expense_heads', 'branch_id')) $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
            if (!Schema::hasColumn('expense_heads', 'description')) $table->text('description')->nullable()->after('code');
        });

        Schema::create('company_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'name']);
        });

        Schema::create('company_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('company_role_id')->constrained('company_roles')->cascadeOnDelete();
            $table->string('module_name');
            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_view')->default(true);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();
            $table->unique(['branch_id', 'company_role_id', 'module_name']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('company_permissions');
        Schema::dropIfExists('company_roles');
        Schema::table('expense_heads', function (Blueprint $table) {
            if (Schema::hasColumn('expense_heads', 'branch_id')) $table->dropConstrainedForeignId('branch_id');
            if (Schema::hasColumn('expense_heads', 'description')) $table->dropColumn('description');
        });
    }
};
