<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('user_id')->constrained('plans')->nullOnDelete();
            $table->string('provider')->nullable()->after('status');
            $table->string('provider_reference')->nullable()->after('provider');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['provider', 'provider_reference']);
        });
    }
};


