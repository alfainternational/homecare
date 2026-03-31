<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // service_requests — most queried table
        Schema::table('service_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('service_type');
            $table->index('priority');
            $table->index('created_at');
            $table->index('scheduled_at');
            $table->index(['client_id', 'status']);
            $table->index(['technician_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // subscriptions
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index('status');
            $table->index('ends_at');
            $table->index(['user_id', 'status']);
        });

        // orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
            $table->index(['user_id', 'status']);
        });

        // addresses
        Schema::table('addresses', function (Blueprint $table) {
            $table->index('is_primary');
            $table->index(['user_id', 'is_primary']);
        });

        // request_reports
        Schema::table('request_reports', function (Blueprint $table) {
            $table->index('type');
            $table->index(['request_id', 'type']);
        });

        // request_media
        Schema::table('request_media', function (Blueprint $table) {
            $table->index('type');
        });

        // wallet_transactions
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index('type');
            $table->index('created_at');
            $table->index(['wallet_id', 'type']);
        });

        // notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('read_at');
            $table->index(['notifiable_id', 'read_at']);
        });

        // products
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('stock');
            $table->index('is_featured');
            $table->index(['category_id', 'is_active']);
            $table->index(['is_active', 'stock']);
        });

        // technician_profiles
        Schema::table('technician_profiles', function (Blueprint $table) {
            $table->index('status');
            $table->index('rating_average');
        });

        // users
        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index(['role', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['service_type']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['scheduled_at']);
            $table->dropIndex(['client_id', 'status']);
            $table->dropIndex(['technician_id', 'status']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
