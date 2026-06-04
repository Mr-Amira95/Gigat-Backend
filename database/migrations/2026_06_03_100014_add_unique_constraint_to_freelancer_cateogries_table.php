<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicates — keep the lowest-id entry per (user_id, category_id)
        DB::statement("
            DELETE fc1 FROM freelancer_cateogries fc1
            INNER JOIN freelancer_cateogries fc2
            ON fc1.user_id = fc2.user_id AND fc1.category_id = fc2.category_id AND fc1.id > fc2.id
        ");

        Schema::table('freelancer_cateogries', function (Blueprint $table) {
            $table->unique(['user_id', 'category_id'], 'freelancer_cateogries_user_category_unique');
        });
    }

    public function down(): void
    {
        Schema::table('freelancer_cateogries', function (Blueprint $table) {
            $table->dropUnique('freelancer_cateogries_user_category_unique');
        });
    }
};
