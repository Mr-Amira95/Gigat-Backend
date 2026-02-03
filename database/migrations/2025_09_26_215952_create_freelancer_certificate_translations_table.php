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
        Schema::create('freelancer_certificate_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_certificate_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('language', 5);
            $table->string('description');
            $table->timestamps();

            $table->unique(['freelancer_certificate_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_certificate_translations');
    }
};
