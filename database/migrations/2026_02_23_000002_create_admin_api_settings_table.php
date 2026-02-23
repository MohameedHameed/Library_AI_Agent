<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_api_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_name');           // e.g. 'google_books', 'openlibrary', 'gutenberg'
            $table->string('display_name');       // e.g. 'Google Books API'
            $table->text('api_key')->nullable();  // encrypted API key
            $table->string('api_url')->nullable();
            $table->enum('status', ['pending', 'approved', 'disabled'])->default('pending');
            $table->text('notes')->nullable();    // admin notes
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_api_settings');
    }
};
