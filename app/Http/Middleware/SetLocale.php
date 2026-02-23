<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priority 1: Check if user is logged in and has preference
        if (Auth::check()) {
            $user = Auth::user();
            $preferences = $user->preferences;
            
            if ($preferences && $preferences->language) {
                App::setLocale($preferences->language);
                Session::put('locale', $preferences->language);
                return $next($request);
            }
        }
        
        // Priority 2: Check session
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
            return $next($request);
        }
        
        // Priority 3: Default to Arabic
        App::setLocale('ar');
        
        return $next($request);
    }
}
