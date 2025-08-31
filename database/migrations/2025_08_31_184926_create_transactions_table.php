<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // Each transaction belongs to an order.
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            // Name of the payment gateway (e.g., 'zarinpal', 'mellat').
            $table->string('gateway_name');
            // The unique transaction ID provided by the gateway.
            $table->string('gateway_transaction_id')->nullable();
            // The amount of this specific transaction attempt.
            $table->unsignedInteger('amount');
            // Status of the transaction.
            $table->enum('status', ['pending', 'verified', 'failed'])->default('pending');
            // To store any extra data from the gateway.
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
