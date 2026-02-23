<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteBook extends Model
{
    protected $fillable = [
        'user_id',
        'recommended_book_id',
    ];

    /**
     * Get the user that owns the favorite.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recommended book.
     */
    public function recommendedBook(): BelongsTo
    {
        return $this->belongsTo(RecommendedBook::class);
    }
}
