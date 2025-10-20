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
        Schema::create('voice_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voice_channel_id')->constrained('voice_channels')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('is_speaking')->default(false);
            $table->boolean('is_muted')->default(false);
            $table->boolean('is_deafened')->default(false);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->json('audio_settings')->nullable(); // For user-specific audio settings
            $table->timestamps();

            $table->index(['voice_channel_id', 'joined_at']);
            $table->index(['user_id', 'joined_at']);
            $table->unique(['voice_channel_id', 'user_id']); // One session per user per channel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voice_sessions');
    }
};
