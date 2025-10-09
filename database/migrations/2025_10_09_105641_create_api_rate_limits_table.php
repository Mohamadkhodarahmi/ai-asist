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
        Schema::create('api_rate_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('api_key_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('endpoint'); // Which API endpoint was called
            $table->string('method', 10); // HTTP method (GET, POST, etc.)
            $table->integer('requests_count')->default(1);
            $table->timestamp('window_start'); // Start of the rate limit window
            $table->timestamp('window_end'); // End of the rate limit window
            $table->timestamps();
            
            $table->index(['user_id', 'window_start', 'window_end']);
            $table->index(['api_key_id', 'window_start', 'window_end']);
            $table->index(['endpoint', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_rate_limits');
    }
};