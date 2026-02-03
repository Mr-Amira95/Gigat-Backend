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
        Schema::create('request_log_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained('request_logs')->cascadeOnDelete();
            $table->string('media_path');
            $table->enum('media_type',['image','video','file']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_log_attachments');
    }
};
