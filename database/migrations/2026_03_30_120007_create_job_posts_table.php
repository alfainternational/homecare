<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->string('post_number')->unique();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('service_category_id')->nullable()->constrained('service_categories')->nullOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignId('winning_bid_id')->nullable(); // filled after client selects

            $table->string('title');
            $table->text('description');
            $table->json('media_paths')->nullable();        // array of uploaded images/videos

            $table->decimal('budget_min', 10, 2)->nullable();
            $table->decimal('budget_max', 10, 2)->nullable();

            $table->enum('status', ['open', 'in_review', 'assigned', 'in_progress', 'completed', 'cancelled'])
                ->default('open');

            $table->timestamp('preferred_date')->nullable();
            $table->timestamp('expires_at')->nullable();      // auto-close after this date
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->index(['client_id', 'status']);
            $table->index(['service_category_id', 'status']);
            $table->index(['status', 'created_at']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
