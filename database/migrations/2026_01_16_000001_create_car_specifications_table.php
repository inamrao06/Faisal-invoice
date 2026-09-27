<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('car_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'type', 'name']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            foreach ([
                'condition_id' => fn () => $table->foreignId('condition_id')->nullable()->after('vehicle_category_id')->constrained('car_specifications')->nullOnDelete(),
                'brand_id' => fn () => $table->foreignId('brand_id')->nullable()->after('condition_id')->constrained('car_specifications')->nullOnDelete(),
                'model_id' => fn () => $table->foreignId('model_id')->nullable()->after('brand_id')->constrained('car_specifications')->nullOnDelete(),
                'fuel_type_id' => fn () => $table->foreignId('fuel_type_id')->nullable()->after('model_id')->constrained('car_specifications')->nullOnDelete(),
                'transmission_type_id' => fn () => $table->foreignId('transmission_type_id')->nullable()->after('fuel_type_id')->constrained('car_specifications')->nullOnDelete(),
            ] as $column => $callback) {
                if (! Schema::hasColumn('vehicles', $column)) {
                    $callback();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            foreach (['condition_id', 'brand_id', 'model_id', 'fuel_type_id', 'transmission_type_id'] as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }
        });
        Schema::dropIfExists('car_specifications');
    }
};
