<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The canonical chat system is chats + chat_messages (created June 2025).
        // conversations + messages (March 2025) are legacy and no longer referenced.
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }

    public function down(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->timestamps();
        });
    }
};
