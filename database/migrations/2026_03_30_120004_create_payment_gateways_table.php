<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();          // mada, tabby, tamara, apple_pay, stc_pay, wallet, bank
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('icon')->nullable();        // SVG path or URL
            $table->boolean('is_enabled')->default(false);
            $table->enum('mode', ['test', 'live'])->default('test');

            // Encrypted credentials
            $table->text('api_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->text('merchant_id')->nullable();
            $table->text('entity_id')->nullable();     // HyperPay entity ID

            // Gateway-specific JSON config
            $table->json('settings')->nullable();      // webhook_url, endpoint, channel, etc.

            // Display
            $table->integer('sort_order')->default(0);
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->decimal('max_amount', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
