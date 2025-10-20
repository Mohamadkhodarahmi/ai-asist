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
        Schema::table('voice_sessions', function (Blueprint $table) {
            // Drop the problematic unique constraint that prevents rejoining
            $table->dropUnique(['voice_channel_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voice_sessions', function (Blueprint $table) {
            // Re-add the unique constraint if needed
            $table->unique(['voice_channel_id', 'user_id']);
        });
    }
};
