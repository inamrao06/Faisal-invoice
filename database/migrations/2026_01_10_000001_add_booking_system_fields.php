<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add extended company fields to branches
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'company_email')) {
                $table->string('company_email', 150)->nullable()->after('email');
            }
            if (!Schema::hasColumn('branches', 'authorized_person')) {
                $table->string('authorized_person', 150)->nullable()->after('manager_name');
            }
            if (!Schema::hasColumn('branches', 'designation')) {
                $table->string('designation', 120)->nullable()->after('authorized_person');
            }
            if (!Schema::hasColumn('branches', 'signature_path')) {
                $table->string('signature_path')->nullable()->after('logo_path');
            }
            if (!Schema::hasColumn('branches', 'stamp_path')) {
                $table->string('stamp_path')->nullable()->after('signature_path');
            }
            if (!Schema::hasColumn('branches', 'website')) {
                $table->string('website', 255)->nullable()->after('stamp_path');
            }
            if (!Schema::hasColumn('branches', 'tax_number')) {
                $table->string('tax_number', 80)->nullable()->after('website');
            }
        });

        // Add user_type and company_role_num to users
        // user_type: super_admin | admin
        // company_role_num: 0 = full access (all access admin), any other = restricted
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', ['super_admin', 'admin'])->default('admin')->after('role');
            }
            if (!Schema::hasColumn('users', 'company_role_num')) {
                $table->unsignedTinyInteger('company_role_num')->default(0)->after('user_type')
                    ->comment('0 = full access; other values = restricted role level');
            }
        });

        // Back-fill user_type from existing role column
        \DB::table('users')->where('role', 'super_admin')->update(['user_type' => 'super_admin']);
        \DB::table('users')->where('role', '!=', 'super_admin')->update(['user_type' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'company_role_num')) $table->dropColumn('company_role_num');
            if (Schema::hasColumn('users', 'user_type')) $table->dropColumn('user_type');
        });

        Schema::table('branches', function (Blueprint $table) {
            foreach (['company_email','authorized_person','designation','signature_path','stamp_path','website','tax_number'] as $col) {
                if (Schema::hasColumn('branches', $col)) $table->dropColumn($col);
            }
        });
    }
};
