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
        Schema::create('technician_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('specializations')->nullable();
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->enum('status', ['available', 'busy', 'off'])->default('available');
            $table->text('bio')->nullable();
            $table->integer('experience_years')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_profiles');
    }
};
