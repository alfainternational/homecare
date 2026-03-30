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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('street')->nullable()->after('client_notes');
            $table->string('district')->nullable()->after('street');
            $table->string('city')->nullable()->after('district');
            $table->unsignedTinyInteger('rating')->nullable()->after('city')->comment('1-5 client rating');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['street', 'district', 'city', 'rating']);
        });
    }
};
