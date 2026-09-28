<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_sale_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicle_sale_invoices', 'seller_customer_id')) {
                $table->foreignId('seller_customer_id')->nullable()->after('customer_id')->constrained('customers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_sale_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_sale_invoices', 'seller_customer_id')) {
                $table->dropConstrainedForeignId('seller_customer_id');
            }
        });
    }
};
