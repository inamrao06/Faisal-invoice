<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_sale_invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('vehicle_sale_invoices', 'commission_amount')) {
                $table->decimal('commission_amount', 16, 2)->default(0)->after('discount');
            }
            if (! Schema::hasColumn('vehicle_sale_invoices', 'commission_notes')) {
                $table->text('commission_notes')->nullable()->after('commission_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_sale_invoices', function (Blueprint $table) {
            foreach (['commission_notes', 'commission_amount'] as $column) {
                if (Schema::hasColumn('vehicle_sale_invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
