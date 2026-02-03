<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->unsignedBigInteger('action_id')->nullable();
            $table->boolean('is_general')->default(false);
            $table->string('icon')->nullable();
            $table->timestamps();

            // Add indexes for faster queries
            $table->index('is_general');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
