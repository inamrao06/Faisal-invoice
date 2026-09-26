<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'location')) $table->string('location')->nullable()->after('city');
            if (!Schema::hasColumn('branches', 'url')) $table->string('url')->nullable()->after('email');
            if (!Schema::hasColumn('branches', 'detail')) $table->text('detail')->nullable()->after('url');
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) $table->string('phone', 30)->nullable()->after('email');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','owner','branch_admin','branch_manager','sales','purchase','service_advisor','technician','cashier','store','hr','accountant','viewer') NOT NULL DEFAULT 'viewer'");
        }
    }

    public function down(): void {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'branch_admin')->update(['role' => 'branch_manager']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','owner','branch_manager','sales','purchase','service_advisor','technician','cashier','store','hr','accountant','viewer') NOT NULL DEFAULT 'viewer'");
        }
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) $table->dropColumn('phone');
        });
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'location')) $table->dropColumn('location');
            if (Schema::hasColumn('branches', 'url')) $table->dropColumn('url');
            if (Schema::hasColumn('branches', 'detail')) $table->dropColumn('detail');
        });
    }
};
