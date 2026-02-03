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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_one');
            $table->unsignedBigInteger('user_id_two');
            $table->timestamps();

            $table->unique(['user_id_one', 'user_id_two']);

            $table->foreign('user_id_one')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_two')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
