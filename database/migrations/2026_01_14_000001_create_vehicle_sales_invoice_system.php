<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('warranty_providers', function (Blueprint $table) {
            foreach ([
                'phone' => fn () => $table->string('phone')->nullable()->after('name'),
                'repairs_phone' => fn () => $table->string('repairs_phone')->nullable()->after('phone'),
                'email' => fn () => $table->string('email')->nullable()->after('repairs_phone'),
                'sales_email' => fn () => $table->string('sales_email')->nullable()->after('email'),
                'repairs_email' => fn () => $table->string('repairs_email')->nullable()->after('sales_email'),
                'terms' => fn () => $table->text('terms')->nullable()->after('repairs_email'),
            ] as $column => $callback) {
                if (! Schema::hasColumn('warranty_providers', $column)) {
                    $callback();
                }
            }
        });

        Schema::create('vehicle_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 60);
            $table->text('classification_text')->nullable();
            $table->text('terms')->nullable();
            $table->foreignId('default_warranty_provider_id')->nullable()->constrained('warranty_providers')->nullOnDelete();
            $table->unsignedSmallInteger('default_warranty_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'code']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'name']);
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('vehicle_category_id')->nullable()->constrained('vehicle_categories')->nullOnDelete();
            $table->string('make_model');
            $table->string('registration_no')->nullable()->index();
            $table->string('vin')->nullable()->index();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedInteger('mileage')->nullable();
            $table->unsignedTinyInteger('keys_count')->nullable();
            $table->decimal('sale_price', 16, 2)->default(0);
            $table->string('status')->default('available')->index();
            $table->timestamps();
        });

        Schema::create('vehicle_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('vehicle_category_id')->nullable()->constrained('vehicle_categories')->nullOnDelete();
            $table->date('invoice_date');
            $table->date('sale_date')->nullable();
            $table->string('transaction_type')->default('vehicle_sale');
            $table->decimal('vehicle_price', 16, 2)->default(0);
            $table->decimal('discount', 16, 2)->default(0);
            $table->decimal('total_sale_price', 16, 2)->default(0);
            $table->decimal('total_paid', 16, 2)->default(0);
            $table->decimal('balance_amount', 16, 2)->default(0);
            $table->string('payment_status')->default('outstanding');
            $table->foreignId('warranty_provider_id')->nullable()->constrained('warranty_providers')->nullOnDelete();
            $table->string('warranty_provider_name')->nullable();
            $table->unsignedSmallInteger('warranty_duration_months')->nullable();
            $table->text('warranty_terms')->nullable();
            $table->text('vehicle_terms')->nullable();
            $table->text('general_terms')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('vehicle_invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_sale_invoice_id')->constrained('vehicle_sale_invoices')->cascadeOnDelete();
            $table->date('payment_date');
            $table->string('payment_type')->default('deposit');
            $table->string('method');
            $table->decimal('amount', 16, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->after('branch_id')->constrained('vehicles')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'vehicle_id')) {
                $table->dropConstrainedForeignId('vehicle_id');
            }
        });
        Schema::dropIfExists('vehicle_invoice_payments');
        Schema::dropIfExists('vehicle_sale_invoices');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('vehicle_categories');
    }
};
