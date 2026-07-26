<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('request_id')->constrained()->nullOnDelete();
            $table->foreignId('portfolio_id')->nullable()->after('service_id')->constrained()->nullOnDelete();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('assigned_to')->references('id')->on('admins')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['portfolio_id']);
            $table->dropColumn(['service_id', 'portfolio_id']);
        });
    }
};
