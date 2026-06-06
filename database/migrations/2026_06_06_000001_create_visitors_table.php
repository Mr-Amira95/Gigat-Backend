<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_uuid', 64)->unique();
            $table->enum('platform', ['web', 'android', 'ios'])->default('web');
            $table->string('device_type', 50)->nullable();
            $table->string('device_os', 100)->nullable();
            $table->string('device_browser', 100)->nullable();
            $table->string('device_model', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_visitors');
    }
};
