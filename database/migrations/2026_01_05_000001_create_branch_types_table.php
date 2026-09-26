<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('branch_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 120);
            $table->string('description', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('branch_types')->insertOrIgnore([
            ['code' => 'car_sale_purchase', 'name' => 'Car Sales / Purchase', 'description' => 'Vehicle inventory, purchases, sales and broker operations.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'service_booking', 'name' => 'Service Booking / Workshop', 'description' => 'Workshop bookings, job cards and technician workflows.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pos', 'name' => 'POS / Parts Store', 'description' => 'Parts stock, POS purchases and POS sales.', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE branches MODIFY type VARCHAR(50) NOT NULL');
        }
    }

    public function down(): void {
        Schema::dropIfExists('branch_types');
    }
};
