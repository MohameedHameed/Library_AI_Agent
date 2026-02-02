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
        Schema::create('recommended_books_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('book_api_id'); // Store the book ID from the external API
            $table->json('book_data')->nullable(); // Store book details from API as JSON
            $table->string('source')->nullable(); // e.g., 'ai_recommendation', 'user_search', etc.
            $table->integer('score')->nullable(); // Recommendation score
            $table->timestamps();

            // Index for faster lookups
            $table->index(['user_id', 'book_api_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommended_books_tables');
    }
};
