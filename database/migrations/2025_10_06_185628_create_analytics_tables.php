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
        // Chat Analytics Table
        Schema::create('chat_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('question');
            $table->text('answer');
            $table->integer('response_time_ms')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->json('metadata')->nullable(); // Store additional data like model used, etc.
            $table->timestamp('created_at');
        });

        // Document Analytics Table
        Schema::create('document_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('document_name');
            $table->string('document_type');
            $table->integer('file_size_bytes');
            $table->integer('pages_count')->nullable();
            $table->integer('questions_asked')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
        });

        // User Activity Analytics
        Schema::create('user_activity_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('activity_type'); // 'login', 'chat', 'upload', 'export', etc.
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
        });

        // API Usage Analytics (for Pro+ plans)
        Schema::create('api_usage_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('endpoint');
            $table->string('method');
            $table->integer('response_time_ms');
            $table->integer('status_code');
            $table->string('ip_address')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage_analytics');
        Schema::dropIfExists('user_activity_analytics');
        Schema::dropIfExists('document_analytics');
        Schema::dropIfExists('chat_analytics');
    }
};
