<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faq_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained()->onDelete('cascade');
            $table->string('language', 5);
            $table->string('question');
            $table->text('answer');
            $table->string('media_path')->nullable();
            $table->enum('media_type',['image','video']);
            $table->timestamps();

            // Add unique constraint
            $table->unique(['faq_id', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faq_translations');
    }
};
