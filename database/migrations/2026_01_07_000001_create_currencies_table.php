<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 80);
            $table->string('symbol', 10)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('currencies')->insertOrIgnore([
            ['code' => 'PKR', 'name' => 'Pakistani Rupee', 'symbol' => 'PKR', 'is_default' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'AED', 'name' => 'UAE Dirham', 'symbol' => 'AED', 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'currency_id')) $table->foreignId('currency_id')->nullable()->after('type')->constrained('currencies')->nullOnDelete();
        });

        $defaultCurrencyId = DB::table('currencies')->where('is_default', true)->value('id');
        if ($defaultCurrencyId) DB::table('branches')->whereNull('currency_id')->update(['currency_id' => $defaultCurrencyId]);
    }

    public function down(): void {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'currency_id')) $table->dropConstrainedForeignId('currency_id');
        });
        Schema::dropIfExists('currencies');
    }
};
