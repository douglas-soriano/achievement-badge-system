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
        Schema::create('achievement_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('achievement_id')->constrained();
            $table->string('type'); // "total_lessons_watched", "total_comments"
            $table->integer('value'); // 1, 5, 10
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievement_requirements');
    }
};
