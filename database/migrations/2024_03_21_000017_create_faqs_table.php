<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs('faqable'); // This is the correct way to make morphs nullable
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add index for faster queries
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
