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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text("response_onesignal")->nullable();
            $table->string('onesignal_id')->nullable();
            $table->json('title');
            $table->json('body');
            $table->string('type')->nullable();
            $table->string('type_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('sent_by_admin')->default(false);
            $table->timestamps();

            $table->index('user_id');   // 👈 Index on user_id
            $table->index('is_read');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
