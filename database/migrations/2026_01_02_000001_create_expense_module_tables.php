<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('expense_heads', function(Blueprint $table){$table->id();$table->string('name');$table->string('code',30)->unique();$table->boolean('is_active')->default(true);$table->timestamps();});
        Schema::create('expenses', function(Blueprint $table){$table->id();$table->string('invoice_no')->unique();$table->foreignId('branch_id')->constrained();$table->foreignId('expense_head_id')->constrained();$table->date('expense_date')->index();$table->string('payment_method',30);$table->text('remarks')->nullable();$table->string('attachment_path')->nullable();$table->decimal('subtotal',16,2)->default(0);$table->decimal('tax_amount',16,2)->default(0);$table->decimal('total_amount',16,2)->default(0);$table->enum('status',['draft','submitted','approved','paid','rejected'])->default('draft');$table->foreignId('created_by')->constrained('users');$table->timestamps();$table->softDeletes();});
        Schema::create('expense_items', function(Blueprint $table){$table->id();$table->foreignId('expense_id')->constrained()->cascadeOnDelete();$table->text('description');$table->decimal('quantity',12,2)->default(1);$table->decimal('rate',16,2)->default(0);$table->decimal('tax_percent',7,2)->default(0);$table->decimal('tax_amount',16,2)->default(0);$table->decimal('line_total',16,2)->default(0);$table->timestamps();});
    }
    public function down(): void {Schema::dropIfExists('expense_items');Schema::dropIfExists('expenses');Schema::dropIfExists('expense_heads');}
};
