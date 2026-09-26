<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key')->unique();        // e.g. booking_confirmed
            $table->string('title', 180);                // notification title
            $table->text('body');                         // notification body (supports {{placeholders}})
            $table->string('channel', 40)->default('push'); // push | sms | email | all
            $table->string('icon', 80)->nullable();       // icon name or URL
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('notification_templates');
    }
};
