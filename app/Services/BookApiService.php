<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookApiService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        // Configure your book API endpoint here
        // Example: Google Books API, Open Library API, etc.
        $this->apiUrl = env('BOOK_API_URL', 'https://www.googleapis.com/books/v1');
        $this->apiKey = env('BOOK_API_KEY', '');
    }

    /**
     * Search for books by query
     * 
     * @param string $query Search query
     * @param int $maxResults Maximum number of results
     * @return array
     */
    public function searchBooks($query, $maxResults = 10)
    {
        try {
            $cacheKey = "book_search_" . md5($query . $maxResults);

            return Cache::remember($cacheKey, 3600, function () use ($query, $maxResults) {
                $response = Http::get("{$this->apiUrl}/volumes", [
                    'q' => $query,
                    'maxResults' => $maxResults,
                    'key' => $this->apiKey,
                    'langRestrict' => 'ar', // Arabic books
                ]);

                if ($response->successful()) {
                    return $this->formatSearchResults($response->json());
                }

                return [];
            });
        } catch (\Exception $e) {
            Log::error('Book API search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get book details by API ID
     * 
     * @param string $bookApiId
     * @return array|null
     */
    public function getBookDetails($bookApiId)
    {
        try {
            $cacheKey = "book_details_" . $bookApiId;

            return Cache::remember($cacheKey, 86400, function () use ($bookApiId) {
                $response = Http::get("{$this->apiUrl}/volumes/{$bookApiId}", [
                    'key' => $this->apiKey,
                ]);

                if ($response->successful()) {
                    return $this->formatBookDetails($response->json());
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Book API details error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format search results to a consistent structure
     * 
     * @param array $apiResponse
     * @return array
     */
    protected function formatSearchResults($apiResponse)
    {
        if (!isset($apiResponse['items'])) {
            return [];
        }

        return array_map(function ($item) {
            return $this->formatBookDetails($item);
        }, $apiResponse['items']);
    }

    /**
     * Format book details to a consistent structure
     * 
     * @param array $item
     * @return array
     */
    protected function formatBookDetails($item)
    {
        $volumeInfo = $item['volumeInfo'] ?? [];

        return [
            'api_id' => $item['id'] ?? null,
            'title' => $volumeInfo['title'] ?? 'Unknown',
            'authors' => $volumeInfo['authors'] ?? [],
            'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown',
            'description' => $volumeInfo['description'] ?? '',
            'publisher' => $volumeInfo['publisher'] ?? '',
            'published_date' => $volumeInfo['publishedDate'] ?? '',
            'page_count' => $volumeInfo['pageCount'] ?? 0,
            'categories' => $volumeInfo['categories'] ?? [],
            'language' => $volumeInfo['language'] ?? 'ar',
            'cover_image' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'isbn' => $this->extractIsbn($volumeInfo['industryIdentifiers'] ?? []),
            'preview_link' => $volumeInfo['previewLink'] ?? null,
        ];
    }

    /**
     * Extract ISBN from industry identifiers
     * 
     * @param array $identifiers
     * @return string|null
     */
    protected function extractIsbn($identifiers)
    {
        foreach ($identifiers as $identifier) {
            if (in_array($identifier['type'], ['ISBN_13', 'ISBN_10'])) {
                return $identifier['identifier'];
            }
        }
        return null;
    }

    /**
     * Get book recommendations based on user preferences
     * 
     * @param array $preferences
     * @return array
     */
    public function getRecommendations($preferences)
    {
        $query = '';

        if (!empty($preferences['favorite_genres'])) {
            $query .= $preferences['favorite_genres'] . ' ';
        }

        if (!empty($preferences['preferred_theme'])) {
            $query .= $preferences['preferred_theme'] . ' ';
        }

        return $this->searchBooks(trim($query), 20);
    }
}
