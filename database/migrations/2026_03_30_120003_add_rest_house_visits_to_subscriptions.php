<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Bonus free visits granted by admin
            $table->integer('bonus_visits')->default(0)->after('visits_total');
            // Rest-house usage tracking (counted separately)
            $table->integer('rest_house_visits_used')->default(0)->after('bonus_visits');
            $table->integer('rest_house_visits_total')->default(0)->after('rest_house_visits_used');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['bonus_visits', 'rest_house_visits_used', 'rest_house_visits_total']);
        });
    }
};
