<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();

            $table->enum('plan_type', ['monthly', 'annual'])->default('monthly');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');

            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            // Commission model (alternative to subscription)
            $table->boolean('is_commission_model')->default(false);
            $table->decimal('commission_rate', 5, 2)->default(0); // percentage e.g. 10.00

            $table->index(['technician_id', 'status']);
            $table->index('ends_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_subscriptions');
    }
};
