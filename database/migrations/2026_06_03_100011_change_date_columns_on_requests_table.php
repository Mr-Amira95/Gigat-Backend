<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize any non-NULL values that aren't valid dates before altering
        DB::statement("UPDATE requests SET start_date = NULL WHERE start_date NOT REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'");
        DB::statement("UPDATE requests SET end_date   = NULL WHERE end_date   NOT REGEXP '^[0-9]{4}-[0-9]{2}-[0-9]{2}$'");

        Schema::table('requests', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->string('start_date')->nullable()->change();
            $table->string('end_date')->nullable()->change();
        });
    }
};
