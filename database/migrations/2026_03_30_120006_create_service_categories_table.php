<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->string('icon')->nullable();            // emoji or SVG
            $table->string('image')->nullable();
            $table->text('description_ar')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_on_demand')->default(false);    // On-demand service type
            $table->boolean('is_marketplace')->default(false);  // Available for bidding
            $table->boolean('is_subscription')->default(true);  // Available for subscription plans

            // Pricing
            $table->decimal('base_price', 10, 2)->nullable();    // Fixed price for on-demand
            $table->boolean('price_on_request')->default(false); // Price determined by technician bid

            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'is_on_demand']);
            $table->index(['is_active', 'is_marketplace']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
