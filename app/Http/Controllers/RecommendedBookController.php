<?php

namespace App\Http\Controllers;

use App\Models\RecommendedBook;
use App\Services\BookApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RecommendedBookController extends Controller
{
    protected $bookApiService;

    public function __construct(BookApiService $bookApiService)
    {
        $this->bookApiService = $bookApiService;
    }

    /**
     * Display a listing of user's recommended books.
     */
    public function index()
    {
        $user = Auth::user();
        $recommendations = RecommendedBook::where('user_id', $user->id)
            ->orderBy('score', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('recommendations.index', compact('recommendations'));
    }

    /**
     * Get AI-generated recommendations based on user preferences.
     */
    public function getRecommendations()
    {
        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return redirect()->route('preferences.create')
                ->with('error', 'الرجاء إعداد تفضيلاتك أولاً للحصول على توصيات مخصصة');
        }

        Log::info('Generating recommendations for user', [
            'user_id' => $user->id,
            'preferences' => [
                'favorite_genres' => $preferences->favorite_genres,
                'preferred_theme' => $preferences->preferred_theme,
                'difficulty_level' => $preferences->difficulty_level,
            ]
        ]);

        // Delete old AI recommendations (keep user-saved books)
        $deletedCount = RecommendedBook::where('user_id', $user->id)
            ->where('source', 'ai_recommendation')
            ->delete();

        Log::info('Deleted old AI recommendations', ['count' => $deletedCount]);

        // Get recommendations from API based on preferences
        $books = $this->bookApiService->getRecommendations([
            'favorite_genres' => $preferences->favorite_genres,
            'preferred_theme' => $preferences->preferred_theme,
            'difficulty_level' => $preferences->difficulty_level,
        ]);

        Log::info('API returned books', [
            'count' => count($books),
            'sample' => !empty($books) ? array_slice($books, 0, 2) : []
        ]);

        // Check if we got any results
        if (empty($books)) {
            Log::warning('No books returned from API');
            return redirect()->route('recommendations.index')
                ->with('error', 'لم نتمكن من العثور على توصيات مناسبة. جرب تعديل تفضيلاتك.');
        }

        // Save new recommendations to database
        foreach ($books as $index => $book) {
            RecommendedBook::create([
                'user_id' => $user->id,
                'book_api_id' => $book['api_id'],
                'book_data' => $book,
                'source' => 'ai_recommendation',
                'score' => 100 - $index, // Higher score for earlier results
            ]);
        }

        Log::info('Saved recommendations to database', ['count' => count($books)]);

        return redirect()->route('recommendations.index')
            ->with('success', 'تم إنشاء ' . count($books) . ' توصية جديدة بناءً على تفضيلاتك المحدثة! 🎉');
    }

    /**
     * Search for books via API.
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        if (empty($query)) {
            return back()->with('error', 'الرجاء إدخال كلمة بحث');
        }

        $books = $this->bookApiService->searchBooks($query, 20);

        return view('books.search', compact('books', 'query'));
    }

    /**
     * Store a book recommendation from search results.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_api_id' => 'required|string',
        ]);

        $user = Auth::user();
        $bookDetails = $this->bookApiService->getBookDetails($request->book_api_id);

        if (!$bookDetails) {
            return back()->with('error', 'لم يتم العثور على الكتاب');
        }

        RecommendedBook::updateOrCreate(
            [
                'user_id' => $user->id,
                'book_api_id' => $request->book_api_id,
            ],
            [
                'book_data' => $bookDetails,
                'source' => 'user_saved',
                'score' => 50,
            ]
        );

        return back()->with('success', 'تمت إضافة الكتاب إلى قائمتك');
    }

    /**
     * Display the specified book details.
     */
    public function show(string $id)
    {
        $recommendation = RecommendedBook::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('recommendations.show', compact('recommendation'));
    }

    /**
     * Remove the specified book from recommendations.
     */
    public function destroy(string $id)
    {
        $recommendation = RecommendedBook::where('user_id', Auth::id())
            ->findOrFail($id);

        $recommendation->delete();

        return redirect()->route('recommendations.index')
            ->with('success', 'تم حذف الكتاب من قائمتك');
    }
}
