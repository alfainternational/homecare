<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('price', 10, 2);
            $table->integer('estimated_duration')->nullable();  // minutes
            $table->text('message');                           // technician's pitch
            $table->json('attachments')->nullable();           // portfolio images

            $table->enum('status', ['pending', 'accepted', 'rejected', 'withdrawn'])->default('pending');
            $table->timestamp('accepted_at')->nullable();

            $table->index(['job_post_id', 'status']);
            $table->index(['technician_id', 'status']);
            $table->unique(['job_post_id', 'technician_id']); // one bid per tech per post

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_bids');
    }
};
