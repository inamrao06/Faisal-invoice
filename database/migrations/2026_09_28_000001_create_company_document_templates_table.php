<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('company_document_templates')) {
            return;
        }

        Schema::create('company_document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('type', 60);
            $table->string('name');
            $table->longText('body');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['branch_id', 'type', 'is_default', 'is_active'], 'cdt_branch_type_default_active_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_document_templates');
    }
};
