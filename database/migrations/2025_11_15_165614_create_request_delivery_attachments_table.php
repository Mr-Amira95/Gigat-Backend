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
        Schema::create('request_delivery_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_delivery_id');
            $table->string('attachment_path');
            $table->timestamps();

            $table->foreign('request_delivery_id')
                ->references('id')->on('request_deliveries')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_delivery_attachments');
    }
};
