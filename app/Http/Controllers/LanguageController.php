<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switch(Request $request, $lang)
    {
        // Validate language
        if (!in_array($lang, ['en', 'ar'])) {
            $lang = 'ar'; // Default to Arabic
        }

        // Set application locale
        App::setLocale($lang);
        
        // Store in session
        Session::put('locale', $lang);
        
        // If user is logged in, update their preference
        if (Auth::check()) {
            $user = Auth::user();
            $preferences = $user->preferences;
            
            if ($preferences) {
                $preferences->update(['language' => $lang]);
            }
        }

        return redirect()->back();
    }
}
