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
        Schema::create('document_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_category_id')->constrained('document_categories')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('document_content_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_content_id')->constrained('document_content')->cascadeOnDelete();
            $table->string('language', 5);
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
            $table->unique(['document_content_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_contents');
    }
};
