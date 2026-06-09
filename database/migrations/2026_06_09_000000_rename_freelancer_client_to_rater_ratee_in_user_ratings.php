<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->dropForeign(['freelancer_id']);
            $table->dropForeign(['client_id']);

            $table->renameColumn('freelancer_id', 'rater_id');
            $table->renameColumn('client_id', 'ratee_id');

            $table->foreign('rater_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('ratee_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_ratings', function (Blueprint $table) {
            $table->dropForeign(['rater_id']);
            $table->dropForeign(['ratee_id']);

            $table->renameColumn('rater_id', 'freelancer_id');
            $table->renameColumn('ratee_id', 'client_id');

            $table->foreign('freelancer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('client_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
