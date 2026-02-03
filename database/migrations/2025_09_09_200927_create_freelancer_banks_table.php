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
         Schema::create('freelancer_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('freelancer_id')->index();
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('iban');
            $table->string('swift_code')->nullable();
            $table->timestamps();

            $table->unique('freelancer_id');
            $table->foreign('freelancer_id')
                  ->references('id')->on('users')  // or 'freelancers' if you have a separate table
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freelancer_banks');
    }
};
