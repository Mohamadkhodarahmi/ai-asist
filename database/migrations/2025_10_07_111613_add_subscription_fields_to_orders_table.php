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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly')->after('amount');
            $table->timestamp('billing_cycle_start')->nullable()->after('billing_period');
            $table->timestamp('billing_cycle_end')->nullable()->after('billing_cycle_start');
            $table->timestamp('trial_ends_at')->nullable()->after('billing_cycle_end');
            $table->boolean('is_recurring')->default(false)->after('trial_ends_at');
            $table->string('subscription_id')->nullable()->after('is_recurring');
            $table->json('metadata')->nullable()->after('subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'billing_period',
                'billing_cycle_start',
                'billing_cycle_end',
                'trial_ends_at',
                'is_recurring',
                'subscription_id',
                'metadata',
            ]);
        });
    }
};
