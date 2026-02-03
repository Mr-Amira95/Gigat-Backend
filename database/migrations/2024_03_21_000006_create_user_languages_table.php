<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'native', 'fluent']);
            $table->timestamps();

            // Add unique constraint to prevent duplicate language entries for a user
            $table->unique(['user_id', 'language_id']);

            // Add index for faster queries
            $table->index('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_languages');
    }
};
