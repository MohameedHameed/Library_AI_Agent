<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\RecommendedBookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Test route to check API connectivity
Route::get('/test-api', function () {
    $service = new \App\Services\BookApiService();

    // Test search
    $results = $service->searchBooks('science', 5);

    return response()->json([
        'test' => 'BookApiService Test',
        'query' => 'science',
        'results_count' => count($results),
        'results' => $results,
    ]);
});

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User preferences routes
    Route::get('/preferences/create', [UserPreferenceController::class, 'create'])->name('preferences.create');
    Route::post('/preferences', [UserPreferenceController::class, 'store'])->name('preferences.store');
    Route::get('/preferences', [UserPreferenceController::class, 'edit'])->name('preferences.edit');
    Route::patch('/preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

    // Book search route
    Route::get('/books/search', [RecommendedBookController::class, 'search'])->name('books.search');

    // Recommended books routes
    Route::get('/recommendations', [RecommendedBookController::class, 'index'])->name('recommendations.index');
    Route::get('/recommendations/generate', [RecommendedBookController::class, 'getRecommendations'])->name('recommendations.generate');
    Route::post('/recommendations', [RecommendedBookController::class, 'store'])->name('recommendations.store');
    Route::get('/recommendations/{id}', [RecommendedBookController::class, 'show'])->name('recommendations.show');
    Route::delete('/recommendations/{id}', [RecommendedBookController::class, 'destroy'])->name('recommendations.destroy');
});

require __DIR__ . '/auth.php';
