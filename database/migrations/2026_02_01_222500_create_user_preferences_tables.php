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
        Schema::create('user_preferences_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); 
            $table->string('favorite_genres')->nullable();
            $table->string('preferred_theme')->nullable();
            $table->string('difficulty_level')->nullable();
            $table->string('price_range')->nullable();
            $table->string('language', 2)->default('ar'); // 'ar' or 'en'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences_tables');
    }
};
