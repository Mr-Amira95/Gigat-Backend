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
        Schema::create('request_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_feature_id');
            $table->string('language', 5);
            $table->string('title');

            $table->timestamps();

            $table->unique(['request_feature_id', 'language']);

            $table->foreign('request_feature_id')
                ->references('id')->on('request_features')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_feature_translations');
    }
};
