<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visitor_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('event_name', 100)->index();
            $table->string('screen_name', 100)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->foreign('visitor_id')->references('id')->on('analytics_visitors')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            // Composite index for time-series dashboard queries
            $table->index(['event_name', 'created_at']);
            $table->index(['visitor_id', 'event_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
