<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();     // Internal ref: PT-20260330-XXXXXXXX
            $table->string('gateway_code');            // mada, tabby, etc.
            $table->string('gateway_reference')->nullable(); // Gateway's own transaction ID
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('payable');                 // Order, ServiceRequest, Subscription

            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->enum('status', ['pending', 'paid', 'failed', 'refunded', 'cancelled'])->default('pending');

            $table->string('redirect_url')->nullable(); // URL to return to after payment
            $table->string('checkout_id')->nullable(); // HyperPay checkoutId
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->index(['user_id', 'status']);
            $table->index(['gateway_code', 'status']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
