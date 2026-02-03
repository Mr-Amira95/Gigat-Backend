<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('language', 5);
            $table->string('title');
            $table->text('description');
            $table->timestamps();

            // Add unique constraint
            $table->unique(['notification_id', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_translations');
    }
};
