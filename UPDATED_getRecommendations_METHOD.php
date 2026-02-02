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

    // Add difficulty level keywords to search
    if (!empty($preferences['difficulty_level'])) {
    $difficultyKeywords = [
    'beginner' => 'مبتدئ بسيط سهل beginner simple easy introduction',
    'intermediate' => 'متوسط intermediate moderate',
    'advanced' => 'متقدم متخصص advanced expert specialized academic',
    ];

    $level = $preferences['difficulty_level'];
    if (isset($difficultyKeywords[$level])) {
    $query .= $difficultyKeywords[$level] . ' ';
    }
    }

    $query = trim($query);

    // If no query, use popular topics in both Arabic and English
    if (empty($query)) {
    Log::info('No preferences provided, using default query');
    $query = 'روايات تاريخ علوم فلسفة novels history science philosophy';
    }

    Log::info('Getting recommendations', [
    'query' => $query,
    'preferences' => $preferences,
    'difficulty_level' => $preferences['difficulty_level'] ?? 'none'
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