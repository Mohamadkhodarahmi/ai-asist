<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // In the migration file
    public function up(): void
    {
        Schema::create('knowledge_files', function (Blueprint $table) {
            $table->id();
            // Each file belongs to a business
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('storage_path');
            // Status to track the processing
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_files');
    }
};
