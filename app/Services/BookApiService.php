<?php

namespace App\Services;

use App\Models\AdminApiSetting;
use App\Models\ApiUsageLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BookApiService
{
    protected $openLibraryUrl = 'https://openlibrary.org';
    protected $gutenbergUrl = 'https://gutendex.com';
    protected $googleBooksUrl = 'https://www.googleapis.com/books/v1';

    /**
     * Log an API call to the api_usage_logs table.
     */
    protected function logUsage(string $apiSource, string $query, string $action, bool $success, int $resultsCount, int $responseTimeMs, ?string $errorMessage = null): void
    {
        try {
            ApiUsageLog::create([
                'user_id'          => Auth::id(),
                'api_source'       => $apiSource,
                'query'            => $query,
                'action'           => $action,
                'success'          => $success,
                'results_count'    => $resultsCount,
                'response_time_ms' => $responseTimeMs,
                'error_message'    => $errorMessage,
                'ip_address'       => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to write API usage log: ' . $e->getMessage());
        }
    }

    /**
     * Resolve the Google Books API key: DB setting takes priority over .env.
     */
    protected function resolveGoogleApiKey(): ?string
    {
        $dbSetting = Cache::remember('admin_google_books_setting', 300, function () {
            return AdminApiSetting::where('api_name', 'google_books')
                ->where('status', 'approved')
                ->first();
        });

        if ($dbSetting && !empty($dbSetting->api_key)) {
            return $dbSetting->api_key;
        }

        return env('GOOGLE_BOOKS_API_KEY');
    }

    /**
     * Check whether a given API is approved in the admin settings.
     * Falls back to ENABLED if no record exists yet (so the app works before seeding).
     * Result is cached for 5 minutes to avoid a DB hit on every search.
     */
    protected function isApiEnabled(string $apiName): bool
    {
        $status = Cache::remember('admin_api_status_' . $apiName, 300, function () use ($apiName) {
            $setting = AdminApiSetting::where('api_name', $apiName)->first();
            return $setting ? $setting->status : 'approved'; // default: approved if no record
        });

        return $status === 'approved';
    }

    /**
     * Search for books by query using OpenLibrary, Gutenberg, and Google Books
     * 
     * @param string $query Search query
     * @param int $maxResults Maximum number of results
     * @param string $language Language filter ('ar' for Arabic, 'en' for English)
     * @return array
     */
    public function searchBooks($query, $maxResults = 20, $language = 'ar')
    {
        try {
            $cacheKey = "book_search_" . md5($query . $maxResults . $language);

            return Cache::remember($cacheKey, 3600, function () use ($query, $maxResults, $language) {
                // Increase execution time for this operation
                set_time_limit(60);
                
                $books = [];

                Log::info('Starting book search across all APIs', [
                    'query' => $query,
                    'language' => $language
                ]);

                // Search OpenLibrary (fastest, try first)
                try {
                    if ($this->isApiEnabled('openlibrary')) {
                        $openLibraryBooks = $this->searchOpenLibrary($query, $maxResults, $language);
                        Log::info('OpenLibrary results', ['count' => count($openLibraryBooks)]);
                        $books = array_merge($books, $openLibraryBooks);
                    } else {
                        Log::info('OpenLibrary skipped — disabled by admin.');
                    }
                } catch (\Exception $e) {
                    Log::warning('OpenLibrary search failed', ['error' => $e->getMessage()]);
                }

                // Search Gutenberg
                try {
                    if ($this->isApiEnabled('gutenberg')) {
                        $gutenbergBooks = $this->searchGutenberg($query, $maxResults, $language);
                        Log::info('Gutenberg results', ['count' => count($gutenbergBooks)]);
                        $books = array_merge($books, $gutenbergBooks);
                    } else {
                        Log::info('Gutenberg skipped — disabled by admin.');
                    }
                } catch (\Exception $e) {
                    Log::warning('Gutenberg search failed', ['error' => $e->getMessage()]);
                }

                // Search Google Books
                try {
                    if ($this->isApiEnabled('google_books')) {
                        $googleBooks = $this->searchGoogleBooks($query, $maxResults, $language);
                        Log::info('Google Books results', ['count' => count($googleBooks)]);
                        $books = array_merge($books, $googleBooks);
                    } else {
                        Log::info('Google Books skipped — disabled by admin.');
                    }
                } catch (\Exception $e) {
                    Log::warning('Google Books search failed', ['error' => $e->getMessage()]);
                }

                Log::info('Total books before deduplication', ['count' => count($books)]);

                // Remove duplicates and limit results
                $books = $this->removeDuplicates($books);

                Log::info('Total books after deduplication', ['count' => count($books)]);

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
     * @param string $language 'ar' for Arabic, 'en' for English
     * @return array
     */
    protected function searchOpenLibrary($query, $limit = 10, $language = 'ar')
    {
        $startTime = microtime(true);
        try {
            // Map language codes: 'ar' => 'ara', 'en' => 'eng'
            $langCode = $language === 'en' ? 'eng' : 'ara';
            
            // First try with language filter
            $response = Http::timeout(10)->get("{$this->openLibraryUrl}/search.json", [
                'q' => $query,
                'limit' => $limit,
                'language' => $langCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['docs'] ?? [];

                Log::info('OpenLibrary search with language filter', [
                    'query' => $query,
                    'language' => $langCode,
                    'count' => count($results)
                ]);

                if (!empty($results)) {
                    $formatted = $this->formatOpenLibraryResults($results);
                    $this->logUsage('openlibrary', $query, 'search', true, count($formatted), (int)((microtime(true) - $startTime) * 1000));
                    return $formatted;
                }
            }

            // Retry without language filter
            Log::info('No results with language filter, trying without filter');
            $response = Http::timeout(10)->get("{$this->openLibraryUrl}/search.json", [
                'q' => $query,
                'limit' => $limit,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $formatted = $this->formatOpenLibraryResults($data['docs'] ?? []);
                $this->logUsage('openlibrary', $query, 'search', true, count($formatted), (int)((microtime(true) - $startTime) * 1000));
                return $formatted;
            }

            $this->logUsage('openlibrary', $query, 'search', false, 0, (int)((microtime(true) - $startTime) * 1000), 'HTTP request failed');
            Log::warning('OpenLibrary search failed');
            return [];
        } catch (\Exception $e) {
            $this->logUsage('openlibrary', $query, 'search', false, 0, (int)((microtime(true) - $startTime) * 1000), $e->getMessage());
            Log::error('OpenLibrary search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search Gutenberg API
     * 
     * @param string $query
     * @param int $limit
     * @param string $language 'ar' for Arabic, 'en' for English
     * @return array
     */
    protected function searchGutenberg($query, $limit = 10, $language = 'ar')
    {
        $startTime = microtime(true);
        try {
            // Gutenberg uses 'ar' for Arabic, 'en' for English
            $langCode = $language === 'en' ? 'en' : 'ar';
            
            // First try with language filter
            $response = Http::timeout(10)->get("{$this->gutenbergUrl}/books", [
                'search' => $query,
                'languages' => $langCode,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = array_slice($data['results'] ?? [], 0, $limit);

                Log::info('Gutenberg search with language filter', [
                    'query' => $query,
                    'language' => $langCode,
                    'count' => count($results)
                ]);

                if (!empty($results)) {
                    $formatted = $this->formatGutenbergResults($results);
                    $this->logUsage('gutenberg', $query, 'search', true, count($formatted), (int)((microtime(true) - $startTime) * 1000));
                    return $formatted;
                }
            }

            // Retry without language filter
            Log::info('No results from Gutenberg with language filter, trying without filter');
            $response = Http::timeout(10)->get("{$this->gutenbergUrl}/books", [
                'search' => $query,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = array_slice($data['results'] ?? [], 0, $limit);
                $formatted = $this->formatGutenbergResults($results);
                $this->logUsage('gutenberg', $query, 'search', true, count($formatted), (int)((microtime(true) - $startTime) * 1000));
                return $formatted;
            }

            $this->logUsage('gutenberg', $query, 'search', false, 0, (int)((microtime(true) - $startTime) * 1000), 'HTTP request failed');
            Log::warning('Gutenberg search failed');
            return [];
        } catch (\Exception $e) {
            $this->logUsage('gutenberg', $query, 'search', false, 0, (int)((microtime(true) - $startTime) * 1000), $e->getMessage());
            Log::error('Gutenberg search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Search Google Books API
     * 
     * @param string $query
     * @param int $limit
     * @param string $language 'ar' for Arabic, 'en' for English
     * @return array
     */
    protected function searchGoogleBooks($query, $limit = 10, $language = 'ar')
    {
        $startTime = microtime(true);
        try {
            $apiKey = $this->resolveGoogleApiKey();

            // If no API key, skip Google Books to avoid rate limiting
            if (empty($apiKey)) {
                Log::info('Google Books API key not configured, skipping');
                return [];
            }

            // Google Books uses 'ar' for Arabic, 'en' for English
            $langCode = $language === 'en' ? 'en' : 'ar';

            // First try with language restriction
            // Only use paid-ebooks filter for English (Arabic has very few paid books)
            $params = [
                'q' => $query,
                'maxResults' => $limit,
                'langRestrict' => $langCode,
                'key' => $apiKey,
            ];
            
            // Add paid filter only for English to get more paid books
            if ($language === 'en') {
                $params['filter'] = 'paid-ebooks';
            }
            
            $response = Http::timeout(10)->get("{$this->googleBooksUrl}/volumes", $params);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['items'] ?? [];

                Log::info('Google Books search with language filter', [
                    'query' => $query,
                    'language' => $langCode,
                    'count' => count($results)
                ]);

                // If we got results, return them
                if (!empty($results)) {
                    $formatted = $this->formatGoogleBooksResults($results);
                    $this->logUsage('google_books', $query, 'search', true, count($formatted), (int)((microtime(true) - $startTime) * 1000));
                    return $formatted;
                }
            }

            // If no results with language filter, try without language restriction
            Log::info('No results from Google Books with language filter, trying without filter');
            
            $params = [
                'q' => $query,
                'maxResults' => $limit,
                'key' => $apiKey,
            ];
            
            // Add paid filter only for English
            if ($language === 'en') {
                $params['filter'] = 'paid-ebooks';
            }
            
            $response = Http::timeout(10)->get("{$this->googleBooksUrl}/volumes", $params);

            if ($response->successful()) {
                $data = $response->json();
                $formatted = $this->formatGoogleBooksResults($data['items'] ?? []);
                $this->logUsage('google_books', $query, 'search', true, count($formatted), (int)((microtime(true) - $startTime) * 1000));
                return $formatted;
            }

            $this->logUsage('google_books', $query, 'search', false, 0, (int)((microtime(true) - $startTime) * 1000), 'HTTP ' . $response->status());
            Log::warning('Google Books search failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [];
        } catch (\Exception $e) {
            $this->logUsage('google_books', $query, 'search', false, 0, (int)((microtime(true) - $startTime) * 1000), $e->getMessage());
            Log::error('Google Books search error: ' . $e->getMessage(), [
                'query' => $query,
                'exception' => get_class($e)
            ]);
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

        // Get the most accurate publication date
        // Priority: publish_year (most recent) > publish_date > first_publish_year (oldest)
        $publishedDate = '';
        if (!empty($doc['publish_year'])) {
            // publish_year is an array of years from all editions, get the most recent
            $years = is_array($doc['publish_year']) ? $doc['publish_year'] : [$doc['publish_year']];
            $publishedDate = max($years); // Get most recent year
        } elseif (!empty($doc['publish_date'])) {
            // publish_date is an array of dates, get the most recent
            $dates = is_array($doc['publish_date']) ? $doc['publish_date'] : [$doc['publish_date']];
            // Extract years from dates and get the most recent
            $years = array_map(function($date) {
                // Try to extract 4-digit year from various date formats
                if (preg_match('/(\d{4})/', $date, $matches)) {
                    return (int)$matches[1];
                }
                return 0;
            }, $dates);
            $publishedDate = max($years) ?: '';
        } elseif (!empty($doc['first_publish_year'])) {
            // Fallback to first publish year if nothing else available
            $publishedDate = $doc['first_publish_year'];
        }
        
        // Get the most accurate page count
        // Priority: edition_count weighted average > number_of_pages_median > first available
        $pageCount = 0;
        if (!empty($doc['number_of_pages_median'])) {
            $pageCount = $doc['number_of_pages_median'];
        } elseif (!empty($doc['edition_count']) && !empty($doc['number_of_pages'])) {
            // If we have multiple editions, use the median
            $pageCount = $doc['number_of_pages_median'] ?? 0;
        }

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
            'published_date' => $publishedDate,
            'page_count' => $pageCount,
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
        
        // Gutenberg books are public domain, typically old books
        // Most don't have publication dates in API, but we can infer from death_year
        $publishedDate = '';
        if (!empty($book['authors'])) {
            foreach ($book['authors'] as $author) {
                if (!empty($author['death_year'])) {
                    // Estimate publication around author's death year
                    $publishedDate = $author['death_year'];
                    break;
                }
            }
        }

        return [
            'api_id' => 'gutenberg:' . ($book['id'] ?? ''),
            'source' => 'Project Gutenberg',
            'title' => $book['title'] ?? 'Unknown',
            'authors' => $authors,
            'author' => implode(', ', $authors),
            'description' => '', // Gutenberg doesn't provide descriptions
            'publisher' => 'Project Gutenberg',
            'published_date' => $publishedDate,
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
        $saleInfo = $item['saleInfo'] ?? [];
        
        // Extract price information
        $price = null;
        $currency = null;
        $saleability = $saleInfo['saleability'] ?? 'NOT_FOR_SALE';
        
        if ($saleability === 'FOR_SALE' && isset($saleInfo['retailPrice'])) {
            $price = $saleInfo['retailPrice']['amount'] ?? null;
            $currency = $saleInfo['retailPrice']['currencyCode'] ?? 'USD';
        } elseif ($saleability === 'FREE' || $saleability === 'NOT_FOR_SALE') {
            $price = 0;
            $currency = 'FREE';
        }
        
        // Extract only the year from publishedDate
        // Google Books returns dates in various formats: "2024", "2024-01", "2024-01-15"
        $publishedDate = '';
        if (!empty($volumeInfo['publishedDate'])) {
            $dateString = $volumeInfo['publishedDate'];
            // Extract 4-digit year
            if (preg_match('/(\d{4})/', $dateString, $matches)) {
                $publishedDate = $matches[1];
            }
        }

        return [
            'api_id' => 'googlebooks:' . ($item['id'] ?? ''),
            'source' => 'Google Books',
            'title' => $volumeInfo['title'] ?? 'Unknown',
            'authors' => $volumeInfo['authors'] ?? [],
            'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : 'Unknown',
            'description' => $volumeInfo['description'] ?? '',
            'publisher' => $volumeInfo['publisher'] ?? '',
            'published_date' => $publishedDate,
            'page_count' => $volumeInfo['pageCount'] ?? 0,
            'categories' => $volumeInfo['categories'] ?? [],
            'language' => $volumeInfo['language'] ?? 'ar',
            'cover_image' => $volumeInfo['imageLinks']['thumbnail'] ?? null,
            'isbn' => $this->extractIsbnFromGoogle($volumeInfo['industryIdentifiers'] ?? []),
            'preview_link' => $volumeInfo['previewLink'] ?? null,
            'price' => $price,
            'currency' => $currency,
            'saleability' => $saleability,
            'buy_link' => $saleInfo['buyLink'] ?? null,
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
        $keywords = [];
        
        // Extract language preference (default to Arabic)
        $language = $preferences['language'] ?? 'ar';
        
        // Keyword translation map (Arabic to English)
        $translationMap = [
            'روايات' => 'novels',
            'تاريخ' => 'history',
            'علوم' => 'science',
            'فلسفة' => 'philosophy',
            'أدب' => 'literature',
            'شعر' => 'poetry',
            'سيرة ذاتية' => 'biography',
            'تطوير ذات' => 'self development',
            'دين' => 'religion',
            'سياسة' => 'politics',
            'اقتصاد' => 'economics',
            'فن' => 'art',
            'تكنولوجيا' => 'technology',
            'مغامرات' => 'adventure',
            'رومانسية' => 'romance',
            'جريمة' => 'crime',
            'خيال علمي' => 'science fiction',
            'فانتازيا' => 'fantasy',
            'رعب' => 'horror',
            'تشويق' => 'thriller',
            'كوميديا' => 'comedy',
            'دراما' => 'drama',
            'ثقافة' => 'culture',
            'تعليمي' => 'educational',
        ];
        
        // Extract keywords from favorite_genres (comma-separated)
        if (!empty($preferences['favorite_genres'])) {
            $genres = str_replace(',', ' ', $preferences['favorite_genres']);
            $genres = trim($genres);
            
            // Translate if language is English
            if ($language === 'en') {
                $genreWords = explode(' ', $genres);
                $translatedWords = [];
                foreach ($genreWords as $word) {
                    $word = trim($word);
                    if (!empty($word)) {
                        $translatedWords[] = $translationMap[$word] ?? $word;
                    }
                }
                $genres = implode(' ', $translatedWords);
            }
            
            $keywords[] = $genres;
        }
        
        // Extract keywords from preferred_theme (comma-separated)
        if (!empty($preferences['preferred_theme'])) {
            $themes = str_replace(',', ' ', $preferences['preferred_theme']);
            $themes = trim($themes);
            
            // Translate if language is English
            if ($language === 'en') {
                $themeWords = explode(' ', $themes);
                $translatedWords = [];
                foreach ($themeWords as $word) {
                    $word = trim($word);
                    if (!empty($word)) {
                        $translatedWords[] = $translationMap[$word] ?? $word;
                    }
                }
                $themes = implode(' ', $translatedWords);
            }
            
            $keywords[] = $themes;
        }
        
        // Combine all keywords into a single query
        $query = implode(' ', $keywords);
        $query = preg_replace('/\s+/', ' ', trim($query));
        
        // If no query, use popular topics based on language
        if (empty($query)) {
            Log::info('No preferences provided, using default query');
            $query = $language === 'en' 
                ? 'novels history science philosophy' 
                : 'روايات تاريخ علوم فلسفة';
        }
        
        Log::info('Getting recommendations', [
            'query' => $query,
            'language' => $language,
            'preferences' => $preferences
        ]);
        
        // Get books to have enough for filtering (reduced from 40 to 30 for better performance)
        // This reduces API timeout issues while still providing good variety
        $allBooks = $this->searchBooks($query, 30, $language);
        
        // Separate paid and free books
        $paidBooks = [];
        $freeBooks = [];
        
        foreach ($allBooks as $book) {
            if (isset($book['price'])) {
                if ($book['price'] == 0) {
                    $freeBooks[] = $book;
                } else {
                    $paidBooks[] = $book;
                }
            } else {
                // Books without price info are treated as free (public domain)
                $freeBooks[] = $book;
            }
        }
        
        // Filter by book length if specified
        if (!empty($preferences['book_length'])) {
            $allBooks = array_merge($paidBooks, $freeBooks);
            $allBooks = $this->filterByBookLength($allBooks, $preferences['book_length']);
            
            // Re-separate after length filtering
            $paidBooks = [];
            $freeBooks = [];
            
            foreach ($allBooks as $book) {
                if (isset($book['price']) && $book['price'] > 0) {
                    $paidBooks[] = $book;
                } else {
                    $freeBooks[] = $book;
                }
            }
            
            Log::info('Filtered books by length', [
                'book_length' => $preferences['book_length'],
                'count_after_filter' => count($allBooks)
            ]);
        }
        
        // Filter by publication year range if specified
        if (!empty($preferences['publication_year_range'])) {
            $allBooks = array_merge($paidBooks, $freeBooks);
            $allBooks = $this->filterByPublicationYear($allBooks, $preferences['publication_year_range']);
            
            // Re-separate after year filtering
            $paidBooks = [];
            $freeBooks = [];
            
            foreach ($allBooks as $book) {
                if (isset($book['price']) && $book['price'] > 0) {
                    $paidBooks[] = $book;
                } else {
                    $freeBooks[] = $book;
                }
            }
            
            Log::info('Filtered books by publication year', [
                'year_range' => $preferences['publication_year_range'],
                'count_after_filter' => count($allBooks)
            ]);
        }
        
        // Limit to ~50 books each (total up to 100 books)
        $paidBooks = array_slice($paidBooks, 0, 50);
        $freeBooks = array_slice($freeBooks, 0, 50);
        
        // Combine: paid books first, then free books
        $books = array_merge($paidBooks, $freeBooks);
        
        Log::info('Separated paid and free books', [
            'paid_count' => count($paidBooks),
            'free_count' => count($freeBooks),
            'total_count' => count($books)
        ]);
        
        // If no results, try simpler fallback searches
        if (empty($books)) {
            Log::warning('No books found for query, trying fallback searches', ['query' => $query]);
            
            // Try just "books" in the selected language
            $fallbackQuery = $language === 'en' ? 'books' : 'كتب';
            $allBooks = $this->searchBooks($fallbackQuery, 40, $language);
            
            // Separate again
            $paidBooks = [];
            $freeBooks = [];
            
            foreach ($allBooks as $book) {
                if (isset($book['price'])) {
                    if ($book['price'] == 0) {
                        $freeBooks[] = $book;
                    } else {
                        $paidBooks[] = $book;
                    }
                } else {
                    $freeBooks[] = $book;
                }
            }
            
            $paidBooks = array_slice($paidBooks, 0, 50);
            $freeBooks = array_slice($freeBooks, 0, 50);
            $books = array_merge($paidBooks, $freeBooks);
        }
        
        return $books;
    }
    
    /**
     * Filter books by book length (page count)
     * 
     * @param array $books
     * @param string $bookLength ('short', 'medium', 'long')
     * @return array
     */
    protected function filterByBookLength($books, $bookLength)
    {
        $bookLength = strtolower(trim($bookLength));
        
        return array_filter($books, function($book) use ($bookLength) {
            $pageCount = $book['page_count'] ?? 0;
            
            // Skip books without page count info
            if (empty($pageCount) || $pageCount == 0) {
                return true; // Include books without page count
            }
            
            switch ($bookLength) {
                case 'short':
                    // Less than 200 pages
                    return $pageCount < 200;
                    
                case 'medium':
                    // 200-400 pages
                    return $pageCount >= 200 && $pageCount <= 400;
                    
                case 'long':
                    // More than 400 pages
                    return $pageCount > 400;
                    
                default:
                    return true;
            }
        });
    }
    
    /**
     * Filter books by publication year range
     * 
     * @param array $books
     * @param string $yearRange ('recent', 'modern', 'classic')
     * @return array
     */
    protected function filterByPublicationYear($books, $yearRange)
    {
        $currentYear = date('Y');
        $yearRange = strtolower(trim($yearRange));
        
        return array_filter($books, function($book) use ($yearRange, $currentYear) {
            $publishedDate = $book['published_date'] ?? '';
            
            // Skip books without publication date
            if (empty($publishedDate)) {
                return true; // Include books without dates
            }
            
            // Extract year from published_date (could be "2020", "2020-01-01", etc.)
            preg_match('/(\d{4})/', $publishedDate, $matches);
            if (empty($matches)) {
                return true; // Include if we can't parse the year
            }
            
            $year = (int) $matches[1];
            
            switch ($yearRange) {
                case 'recent':
                    // Last 5 years
                    return $year >= ($currentYear - 5);
                    
                case 'modern':
                    // 2000-2020
                    return $year >= 2000 && $year <= 2020;
                    
                case 'classic':
                    // Before 2000
                    return $year < 2000;
                    
                default:
                    return true;
            }
        });
    }
}
