<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('warranty_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'name']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'warranty_provider_id')) {
                $table->foreignId('warranty_provider_id')->nullable()->after('payment_method')->constrained('warranty_providers')->nullOnDelete();
            }
            if (! Schema::hasColumn('expenses', 'warranty_provider_name')) {
                $table->string('warranty_provider_name')->nullable()->after('warranty_provider_id');
            }
            if (! Schema::hasColumn('expenses', 'warranty_duration_months')) {
                $table->unsignedSmallInteger('warranty_duration_months')->nullable()->after('warranty_provider_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'warranty_provider_id')) {
                $table->dropConstrainedForeignId('warranty_provider_id');
            }
            foreach (['warranty_provider_name', 'warranty_duration_months'] as $column) {
                if (Schema::hasColumn('expenses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('warranty_providers');
    }
};
