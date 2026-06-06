<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Superseded by 2025_06_16_063045_create_notifications_table.php
        // which defines the canonical notifications schema. This migration
        // is intentionally a no-op to preserve the migrations history.
    }

    public function down(): void
    {
        // No-op — canonical table is managed by 2025_06_16_063045.
    }
};
