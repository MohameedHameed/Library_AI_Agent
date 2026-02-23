<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserPreferenceController extends Controller
{
    /**
     * Show the form for creating user preferences.
     */
    public function create()
    {
        $user = Auth::user();
        $preferences = $user->preferences;

        // If preferences already exist, redirect to edit
        if ($preferences) {
            return redirect()->route('preferences.edit');
        }

        return view('preferences.create');
    }

    /**
     * Store newly created preferences in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'favorite_genres' => 'nullable|array',
            'favorite_genres.*' => 'string',
            'preferred_theme' => 'nullable|array',
            'preferred_theme.*' => 'string',
            'publication_year_range' => 'nullable|in:recent,modern,classic',
            'book_length' => 'nullable|in:short,medium,long',
            'language' => 'nullable|in:ar,en',
        ]);

        $user = Auth::user();

        UserPreference::create([
            'user_id' => $user->id,
            'favorite_genres' => isset($validated['favorite_genres']) ? implode(', ', $validated['favorite_genres']) : null,
            'preferred_theme' => isset($validated['preferred_theme']) ? implode(', ', $validated['preferred_theme']) : null,
            'publication_year_range' => $validated['publication_year_range'] ?? null,
            'book_length' => $validated['book_length'] ?? null,
            'language' => $validated['language'] ?? app()->getLocale(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم حفظ تفضيلاتك بنجاح!');
    }

    /**
     * Show the form for editing user preferences.
     */
    public function edit()
    {
        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return redirect()->route('preferences.create');
        }

        return view('preferences.edit', compact('preferences'));
    }

    /**
     * Update the specified preferences in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'favorite_genres' => 'nullable|array',
            'favorite_genres.*' => 'string',
            'preferred_theme' => 'nullable|array',
            'preferred_theme.*' => 'string',
            'publication_year_range' => 'nullable|in:recent,modern,classic',
            'book_length' => 'nullable|in:short,medium,long',
            'language' => 'nullable|in:ar,en',
        ]);

        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return redirect()->route('preferences.create');
        }

        $preferences->update([
            'favorite_genres' => isset($validated['favorite_genres']) ? implode(', ', $validated['favorite_genres']) : null,
            'preferred_theme' => isset($validated['preferred_theme']) ? implode(', ', $validated['preferred_theme']) : null,
            'publication_year_range' => $validated['publication_year_range'] ?? null,
            'book_length' => $validated['book_length'] ?? null,
            'language' => $validated['language'] ?? $preferences->language,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'تم تحديث تفضيلاتك بنجاح!');
    }
}
