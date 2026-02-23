<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'api_source',
        'query',
        'action',
        'success',
        'results_count',
        'response_time_ms',
        'error_message',
        'ip_address',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    /**
     * Get the user who triggered this log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: only failed requests.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope: only successful requests.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * API source display label.
     */
    public function apiLabel(): string
    {
        return match($this->api_source) {
            'openlibrary'  => 'OpenLibrary',
            'gutenberg'    => 'Project Gutenberg',
            'google_books' => 'Google Books',
            default        => ucfirst($this->api_source),
        };
    }
}
