<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $table = 'user_preferences_tables';

    protected $fillable = [
        'user_id',
        'favorite_genres',
        'preferred_theme',
        'publication_year_range',
        'book_length',
        'language',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
