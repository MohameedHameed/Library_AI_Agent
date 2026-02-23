<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('api_source');           // 'openlibrary', 'gutenberg', 'google_books'
            $table->string('query')->nullable();    // search query used
            $table->string('action')->default('search'); // 'search', 'details', 'recommendations'
            $table->boolean('success')->default(true);
            $table->integer('results_count')->default(0);
            $table->integer('response_time_ms')->nullable(); // milliseconds
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_usage_logs');
    }
};
