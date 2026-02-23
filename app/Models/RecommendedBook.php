<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendedBook extends Model
{
    protected $table = 'recommended_books_tables';

    protected $fillable = [
        'user_id',
        'book_api_id',
        'book_data',
        'source',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
        'book_data' => 'array', // Automatically cast JSON to array
    ];

    /**
     * Get the user that owns the recommendation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all favorites for this recommended book.
     */
    public function favorites()
    {
        return $this->hasMany(FavoriteBook::class, 'recommended_book_id');
    }

    /**
     * Get book title from the cached book_data
     */
    public function getBookTitleAttribute()
    {
        return $this->book_data['title'] ?? 'Unknown';
    }

    /**
     * Get book author from the cached book_data
     */
    public function getBookAuthorAttribute()
    {
        return $this->book_data['author'] ?? 'Unknown';
    }
}
