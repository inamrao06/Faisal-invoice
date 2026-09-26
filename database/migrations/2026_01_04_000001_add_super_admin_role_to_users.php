<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin','admin','owner','branch_admin','branch_manager','sales','purchase','service_advisor','technician','cashier','store','hr','accountant','viewer') NOT NULL DEFAULT 'viewer'");
        }
    }

    public function down(): void {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','owner','branch_manager','sales','purchase','service_advisor','technician','cashier','store','hr','accountant','viewer') NOT NULL DEFAULT 'viewer'");
        }
    }
};
