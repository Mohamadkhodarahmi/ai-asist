<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_bot_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('total_messages')->default(0);
            $table->integer('ai_responses')->default(0);
            $table->integer('user_interactions')->default(0);
            $table->integer('new_users')->default(0);
            $table->integer('active_users')->default(0);
            $table->decimal('avg_response_time', 8, 2)->default(0);
            $table->integer('successful_responses')->default(0);
            $table->integer('failed_responses')->default(0);
            $table->json('popular_commands')->nullable();
            $table->json('user_feedback')->nullable();
            $table->timestamps();
            
            $table->unique(['telegram_bot_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_bot_analytics');
    }
};