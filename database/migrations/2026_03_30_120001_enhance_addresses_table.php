<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            // Address type: home, office, rest_house
            $table->enum('type', ['home', 'office', 'rest_house'])->default('home')->after('label');
            // House/apartment number
            $table->string('address_number')->nullable()->after('street');
            // Friendly label override
            $table->string('map_place_id')->nullable()->after('longitude'); // Google Place ID
            // rest_house visit limits (set per subscription rule)
            $table->integer('max_visits_per_month')->nullable()->after('map_place_id');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn(['type', 'address_number', 'map_place_id', 'max_visits_per_month']);
        });
    }
};
