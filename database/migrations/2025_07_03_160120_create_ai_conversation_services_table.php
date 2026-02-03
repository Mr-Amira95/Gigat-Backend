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
        Schema::create('ai_conversation_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ai_conversation_id');
            $table->unsignedBigInteger('service_id');
            $table->timestamps();

            $table->foreign('ai_conversation_id')
                ->references('id')
                ->on('ai_conversations')
                ->onDelete('cascade');

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversation_services');
    }
};
