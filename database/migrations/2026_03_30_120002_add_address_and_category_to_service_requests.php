<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete()->after('subscription_id');
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete()->after('service_type');
            $table->enum('request_type', ['subscription', 'on_demand', 'marketplace'])->default('subscription')->after('service_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['address_id']);
            $table->dropForeign(['service_category_id']);
            $table->dropColumn(['address_id', 'service_category_id', 'request_type']);
        });
    }
};
