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
        Schema::create('quotation_comment_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_comment_id')->constrained('quotation_comments')->onDelete('cascade');
            $table->string('language', 5);
            $table->text('comment');
            $table->timestamps();

            $table->unique(['quotation_comment_id', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_comment_translations');
    }
};
