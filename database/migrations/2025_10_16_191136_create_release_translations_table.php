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
        Schema::create('release_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id')->constrained('releases')->onDelete('cascade');
            $table->string('language', 5);
            $table->longText('release_note')->nullable();
            $table->timestamps();

            $table->unique(['release_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('release_translations');
    }
};
