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
            'favorite_genres' => 'nullable|string|max:255',
            'preferred_theme' => 'nullable|string|max:255',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'price_range' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        UserPreference::create([
            'user_id' => $user->id,
            'favorite_genres' => $validated['favorite_genres'],
            'preferred_theme' => $validated['preferred_theme'],
            'difficulty_level' => $validated['difficulty_level'],
            'price_range' => $validated['price_range'],
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
            'favorite_genres' => 'nullable|string|max:255',
            'preferred_theme' => 'nullable|string|max:255',
            'difficulty_level' => 'nullable|in:beginner,intermediate,advanced',
            'price_range' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return redirect()->route('preferences.create');
        }

        $preferences->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'تم تحديث تفضيلاتك بنجاح!');
    }
}
