<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_analytics', function (Blueprint $table) {
            // Add foreign key to knowledge_files table
            $table->foreignId('knowledge_file_id')->nullable()->after('business_id')->constrained('knowledge_files')->onDelete('cascade');

            // Add more detailed tracking columns
            $table->integer('total_queries')->default(0)->after('questions_asked');
            $table->integer('successful_queries')->default(0)->after('total_queries');
            $table->integer('failed_queries')->default(0)->after('successful_queries');
            $table->decimal('avg_response_time_ms', 10, 2)->nullable()->after('failed_queries');
            $table->json('search_terms')->nullable()->after('avg_response_time_ms');
            $table->timestamp('uploaded_at')->nullable()->after('search_terms');
        });
    }

    public function down(): void
    {
        Schema::table('document_analytics', function (Blueprint $table) {
            $table->dropForeign(['knowledge_file_id']);
            $table->dropColumn([
                'knowledge_file_id',
                'total_queries',
                'successful_queries',
                'failed_queries',
                'avg_response_time_ms',
                'search_terms',
                'uploaded_at',
            ]);
        });
    }
};
