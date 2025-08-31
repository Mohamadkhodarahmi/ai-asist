<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            // Which user initiated the request (optional, but good to have).
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            // The AI service used (e.g., 'openai', 'gemini').
            $table->string('service_name');
            // The prompt sent to the AI.
            $table->text('prompt');
            // The full response received from the AI.
            $table->longText('response')->nullable();
            // Time taken for the API call in milliseconds.
            $table->unsignedInteger('duration_ms')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
    }
};
