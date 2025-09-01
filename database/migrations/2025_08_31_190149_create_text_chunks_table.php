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
        // This PostgreSQL-specific line has been removed.

        Schema::create('text_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('knowledge_file_id')->constrained()->onDelete('cascade');
            $table->text('content'); // The text is stored in your local MySQL database.
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
