<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.edit_your_preferences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.update_preferences_title') }}
                        </h3>
                        <p class="text-gray-600">{{ __('messages.update_preferences_subtitle') }}</p>
                    </div>

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 ml-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('preferences.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Favorite Genres -->
                        <div>
                            <label for="favorite_genres" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('messages.favorite_genres') }}
                                <span class="text-gray-500 text-xs">{{ __('messages.select_one_or_more') }}</span>
                            </label>
                            @php
                                $selectedGenres = old(
                                    'favorite_genres',
                                    $preferences->favorite_genres ? explode(', ', $preferences->favorite_genres) : [],
                                );
                            @endphp
                            <select name="favorite_genres[]" id="favorite_genres" multiple
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                style="min-height: 120px;">
                                <option value="روايات" {{ in_array('روايات', $selectedGenres) ? 'selected' : '' }}>
                                    روايات - Novels</option>
                                <option value="تاريخ" {{ in_array('تاريخ', $selectedGenres) ? 'selected' : '' }}>تاريخ
                                    - History</option>
                                <option value="علوم" {{ in_array('علوم', $selectedGenres) ? 'selected' : '' }}>علوم -
                                    Science</option>
                                <option value="فلسفة" {{ in_array('فلسفة', $selectedGenres) ? 'selected' : '' }}>فلسفة
                                    - Philosophy</option>
                                <option value="أدب" {{ in_array('أدب', $selectedGenres) ? 'selected' : '' }}>أدب -
                                    Literature</option>
                                <option value="شعر" {{ in_array('شعر', $selectedGenres) ? 'selected' : '' }}>شعر -
                                    Poetry</option>
                                <option value="سيرة ذاتية"
                                    {{ in_array('سيرة ذاتية', $selectedGenres) ? 'selected' : '' }}>سيرة ذاتية -
                                    Biography</option>
                                <option value="تطوير ذات"
                                    {{ in_array('تطوير ذات', $selectedGenres) ? 'selected' : '' }}>تطوير ذات - Self
                                    Development</option>
                                <option value="دين" {{ in_array('دين', $selectedGenres) ? 'selected' : '' }}>دين -
                                    Religion</option>
                                <option value="سياسة" {{ in_array('سياسة', $selectedGenres) ? 'selected' : '' }}>سياسة
                                    - Politics</option>
                                <option value="اقتصاد" {{ in_array('اقتصاد', $selectedGenres) ? 'selected' : '' }}>
                                    اقتصاد - Economics</option>
                                <option value="فن" {{ in_array('فن', $selectedGenres) ? 'selected' : '' }}>فن - Art
                                </option>
                            </select>
                            @error('favorite_genres')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ __('messages.press_ctrl_hint') }}</p>
                        </div>

                        <!-- Preferred Theme -->
                        <div>
                            <label for="preferred_theme" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('messages.preferred_theme') }}
                                <span class="text-gray-500 text-xs">{{ __('messages.select_one_or_more') }}</span>
                            </label>
                            @php
                                $selectedThemes = old(
                                    'preferred_theme',
                                    $preferences->preferred_theme ? explode(', ', $preferences->preferred_theme) : [],
                                );
                            @endphp
                            <select name="preferred_theme[]" id="preferred_theme" multiple
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                style="min-height: 120px;">
                                <option value="تكنولوجيا"
                                    {{ in_array('تكنولوجيا', $selectedThemes) ? 'selected' : '' }}>تكنولوجيا -
                                    Technology</option>
                                <option value="مغامرات" {{ in_array('مغامرات', $selectedThemes) ? 'selected' : '' }}>
                                    مغامرات - Adventure</option>
                                <option value="رومانسية" {{ in_array('رومانسية', $selectedThemes) ? 'selected' : '' }}>
                                    رومانسية - Romance</option>
                                <option value="جريمة" {{ in_array('جريمة', $selectedThemes) ? 'selected' : '' }}>جريمة
                                    - Crime</option>
                                <option value="خيال علمي"
                                    {{ in_array('خيال علمي', $selectedThemes) ? 'selected' : '' }}>خيال علمي - Science
                                    Fiction</option>
                                <option value="فانتازيا" {{ in_array('فانتازيا', $selectedThemes) ? 'selected' : '' }}>
                                    فانتازيا - Fantasy</option>
                                <option value="رعب" {{ in_array('رعب', $selectedThemes) ? 'selected' : '' }}>رعب -
                                    Horror</option>
                                <option value="تشويق" {{ in_array('تشويق', $selectedThemes) ? 'selected' : '' }}>تشويق
                                    - Thriller</option>
                                <option value="كوميديا" {{ in_array('كوميديا', $selectedThemes) ? 'selected' : '' }}>
                                    كوميديا - Comedy</option>
                                <option value="دراما" {{ in_array('دراما', $selectedThemes) ? 'selected' : '' }}>دراما
                                    - Drama</option>
                                <option value="ثقافة" {{ in_array('ثقافة', $selectedThemes) ? 'selected' : '' }}>ثقافة
                                    - Culture</option>
                                <option value="تعليمي" {{ in_array('تعليمي', $selectedThemes) ? 'selected' : '' }}>
                                    تعليمي - Educational</option>
                            </select>
                            @error('preferred_theme')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ __('messages.press_ctrl_hint') }}</p>
                        </div>

                        <!-- Publication Year Range -->
                        <div>
                            <label for="publication_year_range" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('messages.publication_year_range') }}
                                <span class="text-gray-500 text-xs">({{ __('messages.optional') }})</span>
                            </label>
                            <select name="publication_year_range" id="publication_year_range"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                                <option value="">{{ __('messages.any_year') }}</option>
                                <option value="recent"
                                    {{ old('publication_year_range', $preferences->publication_year_range) == 'recent' ? 'selected' : '' }}>
                                    {{ __('messages.recent_books') }}
                                </option>
                                <option value="modern"
                                    {{ old('publication_year_range', $preferences->publication_year_range) == 'modern' ? 'selected' : '' }}>
                                    {{ __('messages.modern_books') }}
                                </option>
                                <option value="classic"
                                    {{ old('publication_year_range', $preferences->publication_year_range) == 'classic' ? 'selected' : '' }}>
                                    {{ __('messages.classic_books') }}
                                </option>
                            </select>
                            @error('publication_year_range')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Book Length -->
                        <div>
                            <label for="book_length" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('messages.book_length') }}
                                <span class="text-gray-500 text-xs">({{ __('messages.optional') }})</span>
                            </label>
                            <select name="book_length" id="book_length"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                                <option value="">{{ __('messages.any_length') }}</option>
                                <option value="short"
                                    {{ old('book_length', $preferences->book_length) == 'short' ? 'selected' : '' }}>
                                    {{ __('messages.short_books') }}
                                </option>
                                <option value="medium"
                                    {{ old('book_length', $preferences->book_length) == 'medium' ? 'selected' : '' }}>
                                    {{ __('messages.medium_books') }}
                                </option>
                                <option value="long"
                                    {{ old('book_length', $preferences->book_length) == 'long' ? 'selected' : '' }}>
                                    {{ __('messages.long_books') }}
                                </option>
                            </select>
                            @error('book_length')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4 pt-4">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md">
                                {{ __('messages.save_preferences') }}
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                                إلغاء
                            </a>
                        </div>

                        <!-- Generate Recommendations Button -->
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('recommendations.generate') }}"
                                class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white text-center font-semibold py-3 px-6 rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md">
                                احصل على توصيات جديدة بناءً على تفضيلاتك
                            </a>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 ml-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900 mb-1">ملاحظة مهمة</h4>
                                    <p class="text-sm text-blue-800">
                                        عند الحصول على توصيات جديدة، سيتم استبدال التوصيات القديمة من الذكاء الاصطناعي
                                        بتوصيات جديدة بناءً على تفضيلاتك المحدثة. الكتب التي حفظتها يدوياً ستبقى في
                                        قائمتك.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
