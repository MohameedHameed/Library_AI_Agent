<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\RecommendedBookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Language switcher route
Route::get('/language/{lang}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');


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
    
    // Favorite books routes
    Route::get('/favorites', [App\Http\Controllers\FavoriteBookController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [App\Http\Controllers\FavoriteBookController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{id}', [App\Http\Controllers\FavoriteBookController::class, 'destroy'])->name('favorites.destroy');

    // Chatbot route
    Route::post('/chatbot/send', [App\Http\Controllers\ChatbotController::class, 'sendMessage'])->name('chatbot.send');
});

// ─── Admin Routes ───────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

    // API Settings
    Route::get('/api-settings', [\App\Http\Controllers\AdminController::class, 'apiSettings'])->name('api-settings');
    Route::patch('/api-settings/{setting}/approve', [\App\Http\Controllers\AdminController::class, 'approveApi'])->name('api-settings.approve');
    Route::patch('/api-settings/{setting}/disable', [\App\Http\Controllers\AdminController::class, 'disableApi'])->name('api-settings.disable');
    Route::patch('/api-settings/{setting}/pending', [\App\Http\Controllers\AdminController::class, 'setPendingApi'])->name('api-settings.pending');
    Route::patch('/api-settings/{setting}', [\App\Http\Controllers\AdminController::class, 'updateApiSetting'])->name('api-settings.update');

    // Usage Logs
    Route::get('/usage-logs', [\App\Http\Controllers\AdminController::class, 'usageLogs'])->name('usage-logs');

    // Users
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('users');
    Route::patch('/users/{user}/promote', [\App\Http\Controllers\AdminController::class, 'promoteUser'])->name('users.promote');
    Route::patch('/users/{user}/demote', [\App\Http\Controllers\AdminController::class, 'demoteUser'])->name('users.demote');
});

require __DIR__ . '/auth.php';
