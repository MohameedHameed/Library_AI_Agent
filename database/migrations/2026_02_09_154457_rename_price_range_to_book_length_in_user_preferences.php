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
            $table->renameColumn('price_range', 'book_length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_preferences_tables', function (Blueprint $table) {
            $table->renameColumn('book_length', 'price_range');
        });
    }
};
