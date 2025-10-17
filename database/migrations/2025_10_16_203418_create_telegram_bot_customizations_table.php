<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_bot_customizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->onDelete('cascade');
            $table->string('bot_name')->nullable();
            $table->text('welcome_message')->nullable();
            $table->text('help_message')->nullable();
            $table->text('error_message')->nullable();
            $table->json('personality_settings')->nullable(); // tone, style, response patterns
            $table->json('keyboard_layout')->nullable(); // custom inline keyboards
            $table->json('commands')->nullable(); // custom /commands
            $table->json('quick_replies')->nullable(); // predefined responses
            $table->string('language', 5)->default('en');
            $table->json('theme_settings')->nullable(); // colors, fonts, etc.
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // additional customization data
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_bot_customizations');
    }
};