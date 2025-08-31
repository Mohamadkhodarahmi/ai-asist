<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        // Enable the vector extension if it's not already enabled.
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('text_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_file_id')->constrained()->onDelete('cascade');
            // The actual text content of the chunk.
            $table->text('content');
            // The vector embedding. The '1536' depends on your embedding model's dimension.
            $table->vector('embedding', 1536); // This is the pgvector type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('text_chunks');
    }
};
