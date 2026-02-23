<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            إعداد تفضيلاتك
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">مرحباً بك!</h3>
                        <p class="text-gray-600">ساعدنا في التعرف على اهتماماتك القرائية لنقدم لك أفضل التوصيات</p>
                    </div>

                    <form method="POST" action="{{ route('preferences.store') }}" class="space-y-6">
                        @csrf

                        <!-- Favorite Genres -->
                        <div>
                            <label for="favorite_genres" class="block text-sm font-medium text-gray-700 mb-2">
                                الأنواع المفضلة
                                <span class="text-gray-500 text-xs">(اختر واحد أو أكثر)</span>
                            </label>
                            <select name="favorite_genres[]" id="favorite_genres" multiple
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                style="min-height: 120px;">
                                <option value="روايات"
                                    {{ in_array('روايات', old('favorite_genres', [])) ? 'selected' : '' }}>روايات -
                                    Novels</option>
                                <option value="تاريخ"
                                    {{ in_array('تاريخ', old('favorite_genres', [])) ? 'selected' : '' }}>تاريخ -
                                    History</option>
                                <option value="علوم"
                                    {{ in_array('علوم', old('favorite_genres', [])) ? 'selected' : '' }}>علوم - Science
                                </option>
                                <option value="فلسفة"
                                    {{ in_array('فلسفة', old('favorite_genres', [])) ? 'selected' : '' }}>فلسفة -
                                    Philosophy</option>
                                <option value="أدب"
                                    {{ in_array('أدب', old('favorite_genres', [])) ? 'selected' : '' }}>أدب - Literature
                                </option>
                                <option value="شعر"
                                    {{ in_array('شعر', old('favorite_genres', [])) ? 'selected' : '' }}>شعر - Poetry
                                </option>
                                <option value="سيرة ذاتية"
                                    {{ in_array('سيرة ذاتية', old('favorite_genres', [])) ? 'selected' : '' }}>سيرة
                                    ذاتية - Biography</option>
                                <option value="تطوير ذات"
                                    {{ in_array('تطوير ذات', old('favorite_genres', [])) ? 'selected' : '' }}>تطوير ذات
                                    - Self Development</option>
                                <option value="دين"
                                    {{ in_array('دين', old('favorite_genres', [])) ? 'selected' : '' }}>دين - Religion
                                </option>
                                <option value="سياسة"
                                    {{ in_array('سياسة', old('favorite_genres', [])) ? 'selected' : '' }}>سياسة -
                                    Politics</option>
                                <option value="اقتصاد"
                                    {{ in_array('اقتصاد', old('favorite_genres', [])) ? 'selected' : '' }}>اقتصاد -
                                    Economics</option>
                                <option value="فن"
                                    {{ in_array('فن', old('favorite_genres', [])) ? 'selected' : '' }}>فن - Art
                                </option>
                            </select>
                            @error('favorite_genres')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">اضغط Ctrl (أو Cmd) لاختيار أكثر من نوع</p>
                        </div>

                        <!-- Preferred Theme -->
                        <div>
                            <label for="preferred_theme" class="block text-sm font-medium text-gray-700 mb-2">
                                المواضيع المفضلة
                                <span class="text-gray-500 text-xs">(اختر واحد أو أكثر)</span>
                            </label>
                            <select name="preferred_theme[]" id="preferred_theme" multiple
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white"
                                style="min-height: 120px;">
                                <option value="تكنولوجيا"
                                    {{ in_array('تكنولوجيا', old('preferred_theme', [])) ? 'selected' : '' }}>تكنولوجيا
                                    - Technology</option>
                                <option value="مغامرات"
                                    {{ in_array('مغامرات', old('preferred_theme', [])) ? 'selected' : '' }}>مغامرات -
                                    Adventure</option>
                                <option value="رومانسية"
                                    {{ in_array('رومانسية', old('preferred_theme', [])) ? 'selected' : '' }}>رومانسية -
                                    Romance</option>
                                <option value="جريمة"
                                    {{ in_array('جريمة', old('preferred_theme', [])) ? 'selected' : '' }}>جريمة - Crime
                                </option>
                                <option value="خيال علمي"
                                    {{ in_array('خيال علمي', old('preferred_theme', [])) ? 'selected' : '' }}>خيال علمي
                                    - Science Fiction</option>
                                <option value="فانتازيا"
                                    {{ in_array('فانتازيا', old('preferred_theme', [])) ? 'selected' : '' }}>فانتازيا -
                                    Fantasy</option>
                                <option value="رعب"
                                    {{ in_array('رعب', old('preferred_theme', [])) ? 'selected' : '' }}>رعب - Horror
                                </option>
                                <option value="تشويق"
                                    {{ in_array('تشويق', old('preferred_theme', [])) ? 'selected' : '' }}>تشويق -
                                    Thriller</option>
                                <option value="كوميديا"
                                    {{ in_array('كوميديا', old('preferred_theme', [])) ? 'selected' : '' }}>كوميديا -
                                    Comedy</option>
                                <option value="دراما"
                                    {{ in_array('دراما', old('preferred_theme', [])) ? 'selected' : '' }}>دراما - Drama
                                </option>
                                <option value="ثقافة"
                                    {{ in_array('ثقافة', old('preferred_theme', [])) ? 'selected' : '' }}>ثقافة -
                                    Culture</option>
                                <option value="تعليمي"
                                    {{ in_array('تعليمي', old('preferred_theme', [])) ? 'selected' : '' }}>تعليمي -
                                    Educational</option>
                            </select>
                            @error('preferred_theme')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">اضغط Ctrl (أو Cmd) لاختيار أكثر من موضوع</p>
                        </div>

                        <!-- Difficulty Level -->
                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                                مستوى الصعوبة
                                <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <select name="difficulty_level" id="difficulty_level"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                                <option value="">اختر مستوى الصعوبة</option>
                                <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>
                                    مبتدئ - كتب سهلة وبسيطة
                                </option>
                                <option value="intermediate"
                                    {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>
                                    متوسط - كتب متوسطة التعقيد
                                </option>
                                <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>
                                    متقدم - كتب متخصصة ومعقدة
                                </option>
                            </select>
                            @error('difficulty_level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label for="price_range" class="block text-sm font-medium text-gray-700 mb-2">
                                النطاق السعري المفضل
                                <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <input type="text" name="price_range" id="price_range" value="{{ old('price_range') }}"
                                placeholder="مثال: 0-50، 50-100، 100+"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('price_range')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4 pt-4">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md">
                                حفظ التفضيلات
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                                تخطي
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
                                    <h4 class="text-sm font-semibold text-blue-900 mb-1">نصيحة</h4>
                                    <p class="text-sm text-blue-800">
                                        كلما كانت تفضيلاتك أكثر تفصيلاً، كانت توصياتنا أكثر دقة وملاءمة لك. يمكنك تعديل
                                        هذه التفضيلات في أي وقت.
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
