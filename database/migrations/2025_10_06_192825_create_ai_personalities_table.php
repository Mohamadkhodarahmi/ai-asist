<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_personalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->default('Default Assistant'); // User-friendly name
            $table->string('tone')->default('professional'); // professional, friendly, casual, formal, etc.
            $table->string('style')->default('helpful'); // helpful, concise, detailed, creative, etc.
            $table->text('system_prompt')->nullable(); // Custom system prompt
            $table->text('greeting_message')->nullable(); // Custom greeting
            $table->json('personality_traits')->nullable(); // Additional traits as JSON
            $table->boolean('is_active')->default(false); // Only one can be active per user
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_personalities');
    }
};
