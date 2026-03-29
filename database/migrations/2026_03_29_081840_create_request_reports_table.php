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
        Schema::create('request_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('service_requests')->cascadeOnDelete();
            $table->enum('type', ['initial', 'final']);
            $table->foreignId('reported_by')->constrained('users')->cascadeOnDelete();
            $table->text('problem_description');
            $table->enum('severity', ['low', 'medium', 'high']);
            $table->json('parts_needed')->nullable();
            $table->integer('estimated_duration')->nullable()->comment('in minutes');
            $table->text('work_done')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('is_approved')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_reports');
    }
};
