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
        Schema::table('users', function (Blueprint $table) {
            // Add the foreign key for the business.
            // It's nullable in case you have users who are not part of a business (e.g., super admins).
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // It's good practice to define the down method to make migrations reversible.
            $table->dropForeign(['business_id']);
            $table->dropColumn('business_id');
        });
    }
};
