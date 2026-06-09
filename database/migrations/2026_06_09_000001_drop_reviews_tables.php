<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('review_translations');
        Schema::dropIfExists('reviews');
    }

    public function down(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating');
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['service_id', 'user_id']);
        });

        Schema::create('review_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->string('language', 10);
            $table->text('comment');
            $table->timestamps();
            $table->unique(['review_id', 'language']);
        });
    }
};
