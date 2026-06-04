<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            // Column already exists — only add the FK constraint
            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
        });
    }
};
