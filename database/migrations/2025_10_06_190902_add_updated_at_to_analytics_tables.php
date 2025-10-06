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
        Schema::table('chat_analytics', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        Schema::table('api_usage_analytics', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('chat_analytics', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });

        Schema::table('api_usage_analytics', function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
};
