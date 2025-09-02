<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('telegram_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('bot_token');
            $table->string('bot_username')->nullable();
            $table->string('webhook_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('telegram_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->onDelete('cascade');
            $table->string('chat_id');
            $table->string('chat_type');
            $table->string('username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->timestamps();
        });

        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_chat_id')->constrained()->onDelete('cascade');
            $table->bigInteger('message_id');
            $table->text('message_text');
            $table->boolean('is_from_bot')->default(false);
            $table->timestamp('telegram_timestamp');
            $table->timestamps();
        });

        Schema::create('telegram_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_bot_id')->constrained()->onDelete('cascade');
            $table->json('payload');
            $table->string('event_type');
            $table->boolean('is_processed')->default(false);
            $table->text('processing_error')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_webhooks');
        Schema::dropIfExists('telegram_messages');
        Schema::dropIfExists('telegram_chats');
        Schema::dropIfExists('telegram_bots');
    }
};