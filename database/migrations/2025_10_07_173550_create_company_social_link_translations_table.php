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
        Schema::create('company_social_link_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_social_link_id')->constrained('company_social_links')->onDelete('cascade');
            $table->string('language', 5);
            $table->string('title');
            $table->timestamps();

            $table->unique(['company_social_link_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_social_link_translations');
    }
};
