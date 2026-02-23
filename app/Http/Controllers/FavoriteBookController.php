<?php

namespace App\Http\Controllers;

use App\Models\FavoriteBook;
use App\Models\RecommendedBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FavoriteBookController extends Controller
{
    /**
     * Display user's favorite books
     */
    public function index()
    {
        $user = Auth::user();
        
        $favorites = FavoriteBook::where('user_id', $user->id)
            ->with('recommendedBook')
            ->latest()
            ->get();
        
        // Extract book data from each favorite
        $favoriteBooks = $favorites->map(function ($favorite) {
            // book_data is already an array (cast in RecommendedBook model)
            $bookData = $favorite->recommendedBook->book_data;
            $bookData['favorite_id'] = $favorite->id;
            $bookData['recommended_book_id'] = $favorite->recommended_book_id;
            return $bookData;
        });
        
        return view('favorites.index', compact('favoriteBooks'));
    }

    /**
     * Add a book to favorites
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recommended_book_id' => 'required|exists:recommended_books_tables,id',
        ]);

        $user = Auth::user();

        try {
            // Check if already favorited
            $existing = FavoriteBook::where('user_id', $user->id)
                ->where('recommended_book_id', $validated['recommended_book_id'])
                ->first();

            if ($existing) {
                return redirect()->back()->with('info', __('messages.already_favorited'));
            }

            FavoriteBook::create([
                'user_id' => $user->id,
                'recommended_book_id' => $validated['recommended_book_id'],
            ]);

            Log::info('Book added to favorites', [
                'user_id' => $user->id,
                'book_id' => $validated['recommended_book_id']
            ]);

            return redirect()->back()->with('success', __('messages.added_to_favorites'));
        } catch (\Exception $e) {
            Log::error('Error adding to favorites', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return redirect()->back()->with('error', __('messages.error_adding_favorite'));
        }
    }

    /**
     * Remove a book from favorites
     */
    public function destroy($id)
    {
        $user = Auth::user();

        $favorite = FavoriteBook::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$favorite) {
            return redirect()->back()->with('error', __('messages.favorite_not_found'));
        }

        $favorite->delete();

        Log::info('Book removed from favorites', [
            'user_id' => $user->id,
            'favorite_id' => $id
        ]);

        return redirect()->back()->with('success', __('messages.removed_from_favorites'));
    }
}
