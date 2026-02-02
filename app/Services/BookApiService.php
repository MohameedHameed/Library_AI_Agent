<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookApiService
{
    protected $openLibraryUrl = 'https://openlibrary.org';
    protected $gutenbergUrl = 'https://gutendex.com';
    protected $googleBooksUrl = 'https://www.googleapis.com/books/v1';

    /**
     * Search for books by query using OpenLibrary, Gutenberg, and Google Books
     * 
     * @param string $query Search query
     * @param int $maxResults Maximum number of results
     * @return array
     */
    public function searchBooks($query, $maxResults = 20)
    {
        try {
            $cacheKey = "book_search_" . md5($query . $maxResults);

            return Cache::remember($cacheKey, 3600, function () use ($query, $maxResults) {
                $books = [];

                // Search OpenLibrary
                $openLibraryBooks = $this->searchOpenLibrary($query, $maxResults);
                $books = array_merge($books, $openLibraryBooks);

                // Search Gutenberg
                $gutenbergBooks = $this->searchGutenberg($query, $maxResults);
                $books = array_merge($books, $gutenbergBooks);

                // Search Google Books (excellent Arabic coverage)
                $googleBooks = $this->searchGoogleBooks($query, $maxResults);
                $books = array_merge($books, $googleBooks);

                // Remove duplicates and limit results
                $books = $this->removeDuplicates($books);
                return array_slice($books, 0, $maxResults);
            });
        } catch (\Exception $e) {
            Log::error('Book API search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search OpenLibrary API
     * 
     * @param string $query
     * @param int $limit
     * @return array
     */
    protected function searchOpenLibrary($query, $limit = 10)
    {
        try {
            // First try with Arabic language filter
            $response = Http::timeout(10)->get("{$this->openLibraryUrl}/search.json", [
                'q' => $query,
                'limit' => $limit,
                'language' => 'ara',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['docs'] ?? [];

                Log::info('OpenLibrary search with Arabic filter', [
                    'query' => $query,
                    'count' => count($results)
                ]);

                // If we got results, return them
                if (!empty($results)) {
                    return $this->formatOpenLibraryResults($results);
                }
            }

            // If no results with Arabic filter, try without language filter
            Log::info('No Arabic results, trying without language filter');
            $response = Http::timeout(10)->get("{$this->openLibraryUrl}/search.json", [
                'q' => $query,
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('OpenLibrary search without filter', [
                    'query' => $query,
                    'count' => count($data['docs'] ?? [])
                ]);
                return $this->formatOpenLibraryResults($data['docs'] ?? []);
            }

            Log::warning('OpenLibrary search failed');
            return [];
        } catch (\Exception $e) {
            Log::error('OpenLibrary search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search Gutenberg API
     * 
     * @param string $query
     * @param int $limit
     * @return array
     */
    protected function searchGutenberg($query, $limit = 10)
    {
        try {
            // First try with Arabic language filter
            $response = Http::timeout(10)->get("{$this->gutenbergUrl}/books", [
                'search' => $query,
                'languages' => 'ar',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = array_slice($data['results'] ?? [], 0, $limit);

                Log::info('Gutenberg search with Arabic filter', [
                    'query' => $query,
                    'count' => count($results)
                ]);

                // If we got results, return them
                if (!empty($results)) {
                    return $this->formatGutenbergResults($results);
                }
            }

            // If no results with Arabic filter, try without language filter
            Log::info('No Arabic results from Gutenberg, trying without language filter');
            $response = Http::timeout(10)->get("{$this->gutenbergUrl}/books", [
                'search' => $query,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = array_slice($data['results'] ?? [], 0, $limit);
                Log::info('Gutenberg search without filter', [
                    'query' => $query,
                    'count' => count($results)
                ]);
                return $this->formatGutenbergResults($results);
            }

            Log::warning('Gutenberg search failed');
            return [];
        } catch (\Exception $e) {
            Log::error('Gutenberg search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search Google Books API
     * 
     * @param string $query
     * @param int $limit
     * @return array
     */
    protected function searchGoogleBooks($query, $limit = 10)
    {
        try {
            // First try with Arabic language restriction
            $response = Http::timeout(10)->get("{$this->googleBooksUrl}/volumes", [
                'q' => $query,
                'maxResults' => $limit,
                'langRestrict' => 'ar', // Arabic books
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['items'] ?? [];

                Log::info('Google Books search with Arabic filter', [
                    'query' => $query,
                    'count' => count($results)
                ]);

                // If we got results, return them
                if (!empty($results)) {
                    return $this->formatGoogleBooksResults($results);
                }
            }

            // If no results with Arabic filter, try without language restriction
            Log::info('No Arabic results from Google Books, trying without language filter');
            $response = Http::timeout(10)->get("{$this->googleBooksUrl}/volumes", [
                'q' => $query,
                'maxResults' => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('Google Books search without filter', [
                    'query' => $query,
                    'count' => count($data['items'] ?? [])
                ]);
                return $this->formatGoogleBooksResults($data['items'] ?? []);
            }

            Log::warning('Google Books search failed');
            return [];
        } catch (\Exception $e) {
            Log::error('Google Books search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get book details by API ID
     * Format: "openlibrary:OLID" or "gutenberg:ID"
     * 
     * @param string $bookApiId
     * @return array|null
     */
    public function getBookDetails($bookApiId)
    {
        try {
            $cacheKey = "book_details_" . $bookApiId;

            return Cache::remember($cacheKey, 86400, function () use ($bookApiId) {
                [$source, $id] = explode(':', $bookApiId, 2);

                if ($source === 'openlibrary') {
                    return $this->getOpenLibraryDetails($id);
                } elseif ($source === 'gutenberg') {
                    return $this->getGutenbergDetails($id);
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::error('Book API details error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get OpenLibrary book details
     * 
     * @param string $workId
     * @return array|null
     */
    protected function getOpenLibraryDetails($workId)
    {
        try {
            $response = Http::timeout(10)->get("{$this->openLibraryUrl}/works/{$workId}.json");

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatOpenLibraryBook($data);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('OpenLibrary details error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Gutenberg book details
     * 
     * @param int $bookId
     * @return array|null
     */
    protected function getGutenbergDetails($bookId)
    {
        try {
            $response = Http::timeout(10)->get("{$this->gutenbergUrl}/books/{$bookId}");

            if ($response->successful()) {
                $data = $response->json();
                return $this->formatGutenbergBook($data);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Gutenberg details error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format OpenLibrary search results
     * 
     * @param array $docs
     * @return array
     */
    protected function formatOpenLibraryResults($docs)
    {
        return array_map(function ($doc) {
            return $this->formatOpenLibraryBook($doc);
        }, $docs);
    }

    /**
     * Format single OpenLibrary book
     * 
     * @param array $doc
     * @return array
     */
    protected function formatOpenLibraryBook($doc)
    {
        $workId = $doc['key'] ?? ($doc['cover_edition_key'] ?? null);
        if (is_string($workId) && strpos($workId, '/works/') !== false) {
            $workId = str_replace('/works/', '', $workId);
        }

        $coverId = $doc['cover_i'] ?? $doc['cover_id'] ?? null;
        $coverImage = $coverId
            ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg"
            : null;

        return [
            'api_id' => 'openlibrary:' . $workId,
            'source' => 'OpenLibrary',
            'title' => $doc['title'] ?? 'Unknown',
            'authors' => $doc['author_name'] ?? [],
            'author' => isset($doc['author_name']) ? implode(', ', $doc['author_name']) : 'Unknown',
            'description' => is_array($doc['description'] ?? null)
                ? ($doc['description']['value'] ?? '')
                : ($doc['description'] ?? ''),
            'publisher' => isset($doc['publisher']) ? (is_array($doc['publisher']) ? implode(', ', $doc['publisher']) : $doc['publisher']) : '',
            'published_date' => $doc['first_publish_year'] ?? '',
            'page_count' => $doc['number_of_pages_median'] ?? 0,
            'categories' => $doc['subject'] ?? [],
            'language' => isset($doc['language']) ? (is_array($doc['language']) ? implode(', ', $doc['language']) : $doc['language']) : 'ar',
            'cover_image' => $coverImage,
            'isbn' => isset($doc['isbn']) ? $doc['isbn'][0] : null,
            'preview_link' => $workId ? "https://openlibrary.org/works/{$workId}" : null,
        ];
    }

    /**
     * Format Gutenberg search results
     * 
     * @param array $results
     * @return array
     */
    protected function formatGutenbergResults($results)
    {
        return array_map(function ($book) {
            return $this->formatGutenbergBook($book);
        }, $results);
    }

    /**
     * Format single Gutenberg book
     * 
     * @param array $book
     * @return array
     */
    protected function formatGutenbergBook($book)
    {
        $authors = array_map(function ($author) {
            return $author['name'] ?? 'Unknown';
        }, $book['authors'] ?? []);

        $subjects = array_merge(
            $book['subjects'] ?? [],
            $book['bookshelves'] ?? []
        );

        return [
            'api_id' => 'gutenberg:' . ($book['id'] ?? ''),
            'source' => 'Project Gutenberg',
            'title' => $book['title'] ?? 'Unknown',
            'authors' => $authors,
            'author' => implode(', ', $authors),
            'description' => '', // Gutenberg doesn't provide descriptions
            'publisher' => 'Project Gutenberg',
            'published_date' => '',
            'page_count' => 0,
            'categories' => $subjects,
            'language' => isset($book['languages']) ? implode(', ', $book['languages']) : 'ar',
            'cover_image' => $book['formats']['image/jpeg'] ?? null,
            'isbn' => null,
            'preview_link' => "https://www.gutenberg.org/ebooks/{$book['id']}",
            'download_links' => $book['formats'] ?? [],
        ];
    }

    /**
     * Format Google Books search results
     * 
     * @param array $items
     * @return array
     */
    protected function formatGoogleBooksResults($items)
    {
        return array_map(function ($item) {
            return $this->formatGoogleBook($item);
        }, $items);
    }

    /**
     * Format single Google Book
     * 
     * @param array $item
     * @return array
     */
    protected function formatGoogleBook($item)
    {
        $volumeInfo = $item['volumeInfo'] ?? [];

        return [
            'api_id' => 'googlebooks:' . ($item['id'] ?? ''),
            'source' => 'Google Books',
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
            'isbn' => $this->extractIsbnFromGoogle($volumeInfo['industryIdentifiers'] ?? []),
            'preview_link' => $volumeInfo['previewLink'] ?? null,
        ];
    }

    /**
     * Extract ISBN from Google Books industry identifiers
     * 
     * @param array $identifiers
     * @return string|null
     */
    protected function extractIsbnFromGoogle($identifiers)
    {
        foreach ($identifiers as $identifier) {
            if (in_array($identifier['type'] ?? '', ['ISBN_13', 'ISBN_10'])) {
                return $identifier['identifier'] ?? null;
            }
        }
        return null;
    }

    /**
     * Remove duplicate books based on title and author
     * 
     * @param array $books
     * @return array
     */
    protected function removeDuplicates($books)
    {
        $unique = [];
        $seen = [];

        foreach ($books as $book) {
            $key = strtolower($book['title'] . '|' . $book['author']);

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $book;
            }
        }

        return $unique;
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

        // Build query from preferences (supports both Arabic and English)
        if (!empty($preferences['favorite_genres'])) {
            $query .= $preferences['favorite_genres'] . ' ';
        }

        if (!empty($preferences['preferred_theme'])) {
            $query .= $preferences['preferred_theme'] . ' ';
        }

        $query = trim($query);

        // If no query, use popular topics in both Arabic and English
        if (empty($query)) {
            Log::info('No preferences provided, using default query');
            $query = 'روايات تاريخ علوم فلسفة novels history science philosophy';
        }

        Log::info('Getting recommendations', [
            'query' => $query,
            'preferences' => $preferences
        ]);

        $books = $this->searchBooks($query, 20);

        // If no results, try simpler fallback searches
        if (empty($books)) {
            Log::warning('No books found for query, trying fallback searches', ['query' => $query]);

            // Try just "books" in both languages
            $books = $this->searchBooks('كتب books', 20);

            // If still no results, try very general search
            if (empty($books)) {
                $books = $this->searchBooks('literature أدب', 20);
            }
        }

        return $books;
    }
}
