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
        Schema::create('request_log_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_log_id')->constrained('request_logs')->onDelete('cascade');
            $table->string('language', 5);
            $table->string('action', 255);
            $table->timestamps();

            $table->unique(['request_log_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_log_translations');
    }
};
