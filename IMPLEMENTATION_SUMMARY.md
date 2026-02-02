# Library AI Agent - Implementation Summary

## Overview
This Laravel application is designed to help users discover books through an AI-powered recommendation system that integrates with external book APIs (like Google Books API).

## Key Features
- **User Preferences**: Users can set their reading preferences (genres, themes, difficulty levels, price ranges)
- **Book Search**: Search for books via external API
- **AI Recommendations**: Get personalized book recommendations based on user preferences
- **Save Books**: Users can save books to their personal library

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
   - `book_api_id`: Book ID from external API (string)
   - `book_data`: JSON field storing complete book information from API
   - `source`: Source of recommendation (ai_recommendation, user_saved, etc.)
   - `score`: Recommendation score (higher = better match)
   - `timestamps`
   - Index on `[user_id, book_api_id]` for faster lookups

## Models

### Book Model
**DELETED** - Books are not stored locally, they come from external API

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
- **Purpose**: Handle all interactions with external book APIs
- **Configuration**:
  - `BOOK_API_URL`: API endpoint (default: Google Books API)
  - `BOOK_API_KEY`: API key (optional for Google Books)

**Methods**:
1. `searchBooks($query, $maxResults = 10)`
   - Search for books by query
   - Results are cached for 1 hour
   - Returns formatted array of books

2. `getBookDetails($bookApiId)`
   - Get detailed information about a specific book
   - Results are cached for 24 hours
   - Returns formatted book array or null

3. `getRecommendations($preferences)`
   - Get book recommendations based on user preferences
   - Builds search query from genres and themes
   - Returns array of recommended books

4. `formatBookDetails($item)`
   - Formats API response to consistent structure
   - Returns standardized book array with fields:
     - api_id, title, authors, author, description, publisher
     - published_date, page_count, categories, language
     - cover_image, isbn, preview_link

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

Add these to your `.env` file:

```env
# Book API Configuration
BOOK_API_URL=https://www.googleapis.com/books/v1
BOOK_API_KEY=  # Optional for Google Books API
```

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
   - System queries external API (Google Books)
   - Results are displayed with book details

4. **Get AI Recommendations**
   - User clicks "Get Recommendations"
   - System uses user preferences to search API
   - Top results are saved as recommendations with scores
   - User can view their personalized recommendations

5. **Save Books**
   - User can save any book from search results
   - Book data is cached in database as JSON
   - User can view/manage their saved books

### Data Flow

```
User Preferences → BookApiService → External API → Format Results → Cache in DB
                                                                   ↓
                                                          Display to User
```

## Next Steps

To complete the application, you need to create:

1. **Views**:
   - `resources/views/preferences/create.blade.php`
   - `resources/views/preferences/edit.blade.php`
   - `resources/views/books/search.blade.php`
   - `resources/views/recommendations/index.blade.php`
   - `resources/views/recommendations/show.blade.php`

2. **API Integration**:
   - Add your Google Books API key to `.env` (optional)
   - Or configure a different book API

3. **Enhanced Features**:
   - Add book ratings/reviews
   - Implement advanced AI recommendation algorithm
   - Add book categories/filters
   - Implement reading lists
   - Add social features (share recommendations)

## Testing

To test the application:

1. Run migrations: `php artisan migrate`
2. Start Vite: `npm run dev`
3. Start Laravel: `php artisan serve`
4. Register a new user
5. Set preferences at `/preferences/create`
6. Search for books on dashboard
7. Generate recommendations at `/recommendations/generate`

## Important Notes

- **Books are NOT stored in the database** - they come from external APIs
- **Book data is cached** in the `recommended_books_tables` as JSON for performance
- **API responses are cached** to reduce API calls and improve performance
- **Arabic language support** is built-in (langRestrict: 'ar' in API calls)
- **RTL support** is enabled in the layout (`dir="rtl"`)
