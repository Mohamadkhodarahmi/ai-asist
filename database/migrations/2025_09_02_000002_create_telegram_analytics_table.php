<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('telegram_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->onDelete('cascade');
            $table->integer('total_messages')->default(0);
            $table->integer('ai_processed_messages')->default(0);
            $table->float('average_response_time')->nullable();
            $table->integer('active_users_count')->default(0);
            $table->integer('successful_responses')->default(0);
            $table->integer('failed_responses')->default(0);
            $table->date('date');
            $table->timestamps();

            $table->unique(['telegram_bot_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_analytics');
    }
};