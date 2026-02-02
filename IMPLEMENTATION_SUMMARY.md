# Library AI Agent - Implementation Summary

## Overview
This Laravel application is designed to help users discover **Arabic books** through an AI-powered recommendation system that integrates with **OpenLibrary**, **Project Gutenberg**, and **Google Books** APIs.

## Key Features
- **User Preferences**: Users can set their reading preferences (genres, themes, difficulty levels, price ranges)
- **Book Search**: Search for Arabic books via OpenLibrary, Gutenberg, and Google Books APIs
- **AI Recommendations**: Get personalized Arabic book recommendations based on user preferences
- **Save Books**: Users can save books to their personal library
- **Triple API Integration**: Combines results from OpenLibrary, Gutenberg, and Google Books for comprehensive Arabic book coverage
- **Arabic Language Priority**: All searches prioritize Arabic books but fall back to other languages for better results

## Database Structure

### Tables

1. **users** (Laravel default)
   - Standard user authentication table

2. **user_preferences_tables**
   - `id`: Primary key
   - `user_id`: Foreign key to users
   - `favorite_genres`: User's favorite book genres
   - `preferred_theme`: Preferred book themes
   - `difficulty_level`: Reading difficulty (beginner, intermediate, advanced)
   - `price_range`: Preferred price range
   - `timestamps`

3. **recommended_books_tables**
   - `id`: Primary key
   - `user_id`: Foreign key to users
   - `book_api_id`: Book ID from external API (string format: "openlibrary:OLID" or "gutenberg:ID")
   - `book_data`: JSON field storing complete book information from API
   - `source`: Source of recommendation (ai_recommendation, user_saved, etc.)
   - `score`: Recommendation score (higher = better match)
   - `timestamps`
   - Index on `[user_id, book_api_id]` for faster lookups

## Models

### Book Model
**DELETED** - Books are not stored locally, they come from external APIs

### UserPreference Model
- **Location**: `app/Models/UserPreference.php`
- **Table**: `user_preferences_tables`
- **Relationships**:
  - `belongsTo(User::class)`
- **Fillable**: user_id, favorite_genres, preferred_theme, difficulty_level, price_range

### RecommendedBook Model
- **Location**: `app/Models/RecommendedBook.php`
- **Table**: `recommended_books_tables`
- **Relationships**:
  - `belongsTo(User::class)`
- **Fillable**: user_id, book_api_id, book_data, source, score
- **Casts**: 
  - `book_data` => 'array' (automatically converts JSON to array)
  - `score` => 'integer'
- **Accessors**:
  - `getBookTitleAttribute()`: Get book title from cached book_data
  - `getBookAuthorAttribute()`: Get book author from cached book_data

### User Model (Updated)
- **Location**: `app/Models/User.php`
- **New Relationships**:
  - `hasOne(UserPreference::class)` - User preferences
  - `hasMany(RecommendedBook::class)` - User's recommended books

## Services

### BookApiService
- **Location**: `app/Services/BookApiService.php`
- **Purpose**: Handle all interactions with OpenLibrary and Gutenberg APIs
- **APIs Used**:
  - **OpenLibrary**: `https://openlibrary.org`
  - **Gutenberg**: `https://gutendex.com`
- **No API Key Required**: Both APIs are free and open

**Methods**:
1. `searchBooks($query, $maxResults = 20)`
   - Searches both OpenLibrary and Gutenberg APIs
   - Merges and deduplicates results
   - Results are cached for 1 hour
   - Returns formatted array of books

2. `searchOpenLibrary($query, $limit = 10)`
   - Searches OpenLibrary API specifically
   - Filters for Arabic books
   - Returns formatted results

3. `searchGutenberg($query, $limit = 10)`
   - Searches Gutenberg API specifically
   - Filters for Arabic books
   - Returns formatted results

4. `getBookDetails($bookApiId)`
   - Get detailed information about a specific book
   - Format: "openlibrary:OLID" or "gutenberg:ID"
   - Results are cached for 24 hours
   - Returns formatted book array or null

5. `getRecommendations($preferences)`
   - Get book recommendations based on user preferences
   - Builds search query from genres and themes
   - Returns array of recommended books from both APIs

6. `removeDuplicates($books)`
   - Removes duplicate books based on title and author
   - Ensures unique results across both APIs

**Book Data Format**:
- `api_id`: Unique identifier (e.g., "openlibrary:OL123W" or "gutenberg:12345")
- `source`: "OpenLibrary" or "Project Gutenberg"
- `title`: Book title
- `authors`: Array of author names
- `author`: Comma-separated author names
- `description`: Book description (OpenLibrary only)
- `publisher`: Publisher name
- `published_date`: Publication date/year
- `page_count`: Number of pages
- `categories`: Array of subjects/categories
- `language`: Book language
- `cover_image`: URL to cover image
- `isbn`: ISBN number (if available)
- `preview_link`: Link to view book online
- `download_links`: Available download formats (Gutenberg only)

## Controllers

### UserPreferenceController
- **Location**: `app/Http/Controllers/UserPreferenceController.php`

**Methods**:
- `create()`: Show form to create preferences
- `store(Request $request)`: Save new preferences
- `edit()`: Show form to edit existing preferences
- `update(Request $request)`: Update existing preferences

### RecommendedBookController
- **Location**: `app/Http/Controllers/RecommendedBookController.php`

**Methods**:
- `index()`: Display user's saved/recommended books
- `getRecommendations()`: Generate AI recommendations based on preferences
- `search(Request $request)`: Search for books via API
- `store(Request $request)`: Save a book to user's library
- `show($id)`: Display book details
- `destroy($id)`: Remove book from user's library

## Routes

### Public Routes
- `GET /`: Welcome page

### Authenticated Routes
All routes below require authentication (`auth` middleware):

**Profile Routes**:
- `GET /profile`: Edit profile
- `PATCH /profile`: Update profile
- `DELETE /profile`: Delete profile

**Preferences Routes**:
- `GET /preferences/create`: Create preferences form
- `POST /preferences`: Store preferences
- `GET /preferences`: Edit preferences form
- `PATCH /preferences`: Update preferences

**Book Search**:
- `GET /books/search`: Search books (query parameter: `search`)

**Recommendations**:
- `GET /recommendations`: View user's saved/recommended books
- `GET /recommendations/generate`: Generate new AI recommendations
- `POST /recommendations`: Save a book (requires `book_api_id`)
- `GET /recommendations/{id}`: View book details
- `DELETE /recommendations/{id}`: Remove book from library

## Environment Configuration

**No configuration needed!** Both OpenLibrary and Gutenberg APIs are free and don't require API keys.

## How It Works

### User Flow

1. **User Registration/Login**
   - User creates account or logs in

2. **Set Preferences**
   - User navigates to `/preferences/create`
   - Fills in favorite genres, themes, difficulty level, price range
   - Preferences are saved to database

3. **Search for Books**
   - User enters search query on dashboard
   - System queries both OpenLibrary and Gutenberg APIs
   - Results from both sources are merged and deduplicated
   - Results are displayed with book details

4. **Get AI Recommendations**
   - User clicks "Get Recommendations"
   - System uses user preferences to search both APIs
   - Top results are saved as recommendations with scores
   - User can view their personalized recommendations

5. **Save Books**
   - User can save any book from search results
   - Book data is cached in database as JSON
   - User can view/manage their saved books

### Data Flow

```
User Preferences → BookApiService → OpenLibrary API ↘
                                                      → Merge & Deduplicate → Cache in DB
                                   → Gutenberg API  ↗                              ↓
                                                                            Display to User
```

## Next Steps

To complete the application, you need to create:

1. **Views** (2 of 5 completed ✅):
   - ✅ `resources/views/preferences/create.blade.php` - DONE
   - ✅ `resources/views/preferences/edit.blade.php` - DONE
   - ⏳ `resources/views/books/search.blade.php` - TODO
   - ⏳ `resources/views/recommendations/index.blade.php` - TODO
   - ⏳ `resources/views/recommendations/show.blade.php` - TODO

2. **API Integration**: ✅ COMPLETE
   - ✅ OpenLibrary API integrated
   - ✅ Gutenberg API integrated
   - ✅ No API keys required

3. **Enhanced Features** (Future):
   - Add book ratings/reviews
   - Implement advanced AI recommendation algorithm
   - Add book categories/filters
   - Implement reading lists
   - Add social features (share recommendations)
   - Add book download functionality (for Gutenberg books)

## Testing

To test the application:

1. Run migrations: `php artisan migrate`
2. Start Vite: `npm run dev`
3. Start Laravel: `php artisan serve`
4. Register a new user
5. Set preferences at `/preferences/create`
6. Search for books on dashboard (searches both OpenLibrary and Gutenberg)
7. Generate recommendations at `/recommendations/generate`

## Important Notes

- **Books are NOT stored in the database** - they come from OpenLibrary and Gutenberg APIs
- **Book data is cached** in the `recommended_books_tables` as JSON for performance
- **API responses are cached** to reduce API calls and improve performance (1 hour for searches, 24 hours for details)
- **Arabic language support** is built-in (`language: 'ara'` for OpenLibrary, `languages: 'ar'` for Gutenberg)
- **RTL support** is enabled in the layout (`dir="rtl"`)
- **No API keys required** - Both APIs are completely free and open
- **Dual source integration** - Results from both APIs are merged and deduplicated for comprehensive coverage
- **Book ID format**: 
  - OpenLibrary: `openlibrary:OL123W`
  - Gutenberg: `gutenberg:12345`

