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
        Schema::table('user_preferences_tables', function (Blueprint $table) {
            $table->renameColumn('difficulty_level', 'publication_year_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_preferences_tables', function (Blueprint $table) {
            $table->renameColumn('publication_year_range', 'difficulty_level');
        });
    }
};
