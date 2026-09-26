<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (! Schema::hasColumn('branches', 'smtp_mailer')) {
                $table->string('smtp_mailer', 20)->nullable()->after('stamp_path');
            }
            if (! Schema::hasColumn('branches', 'smtp_host')) {
                $table->string('smtp_host')->nullable()->after('smtp_mailer');
            }
            if (! Schema::hasColumn('branches', 'smtp_port')) {
                $table->unsignedInteger('smtp_port')->nullable()->after('smtp_host');
            }
            if (! Schema::hasColumn('branches', 'smtp_username')) {
                $table->string('smtp_username')->nullable()->after('smtp_port');
            }
            if (! Schema::hasColumn('branches', 'smtp_password')) {
                $table->string('smtp_password')->nullable()->after('smtp_username');
            }
            if (! Schema::hasColumn('branches', 'smtp_encryption')) {
                $table->string('smtp_encryption', 10)->nullable()->after('smtp_password');
            }
            if (! Schema::hasColumn('branches', 'smtp_from_address')) {
                $table->string('smtp_from_address')->nullable()->after('smtp_encryption');
            }
            if (! Schema::hasColumn('branches', 'smtp_from_name')) {
                $table->string('smtp_from_name')->nullable()->after('smtp_from_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            foreach (['smtp_mailer', 'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_encryption', 'smtp_from_address', 'smtp_from_name'] as $column) {
                if (Schema::hasColumn('branches', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
