<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('media_path');
            $table->enum('media_type',['image','video']); // For storing media type (image, video, etc.)
            $table->boolean('is_cover')->default(false);
            $table->timestamps();

            // Add indexes for faster queries
            $table->index('is_cover');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_media');
    }
};
