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
        Schema::create('request_feedback_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_feedback_id');
            $table->string('language', 5); 
            $table->longText('message')->nullable();
            $table->timestamps();

            $table->unique(['request_feedback_id', 'language']);
            $table->foreign('request_feedback_id')
                ->references('id')->on('request_feedbacks')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_feedback_translations');
    }
};
