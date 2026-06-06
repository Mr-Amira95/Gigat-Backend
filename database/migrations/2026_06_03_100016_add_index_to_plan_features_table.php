<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->index(['service_id', 'type'], 'plan_features_service_type_index');
        });
    }

    public function down(): void
    {
        Schema::table('plan_features', function (Blueprint $table) {
            $table->dropIndex('plan_features_service_type_index');
        });
    }
};
