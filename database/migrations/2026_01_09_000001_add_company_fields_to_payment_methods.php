<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_methods', 'branch_id')) $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->cascadeOnDelete();
            if (!Schema::hasColumn('payment_methods', 'description')) $table->text('description')->nullable()->after('code');
        });
    }

    public function down(): void {
        Schema::table('payment_methods', function (Blueprint $table) {
            if (Schema::hasColumn('payment_methods', 'branch_id')) $table->dropConstrainedForeignId('branch_id');
            if (Schema::hasColumn('payment_methods', 'description')) $table->dropColumn('description');
        });
    }
};
