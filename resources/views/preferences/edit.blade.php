<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            تعديل تفضيلاتك
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">تحديث تفضيلاتك 🎯</h3>
                        <p class="text-gray-600">قم بتحديث اهتماماتك القرائية للحصول على توصيات أفضل</p>
                    </div>

                    <!-- Success Message -->
                    @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 ml-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
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
                                الأنواع المفضلة
                                <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <input
                                type="text"
                                name="favorite_genres"
                                id="favorite_genres"
                                value="{{ old('favorite_genres', $preferences->favorite_genres) }}"
                                placeholder="مثال: روايات، تاريخ، علوم، تطوير ذات"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('favorite_genres')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">يمكنك إدخال عدة أنواع مفصولة بفواصل</p>
                        </div>

                        <!-- Preferred Theme -->
                        <div>
                            <label for="preferred_theme" class="block text-sm font-medium text-gray-700 mb-2">
                                المواضيع المفضلة
                                <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <input
                                type="text"
                                name="preferred_theme"
                                id="preferred_theme"
                                value="{{ old('preferred_theme', $preferences->preferred_theme) }}"
                                placeholder="مثال: تكنولوجيا، فلسفة، سيرة ذاتية، مغامرات"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('preferred_theme')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                                مستوى الصعوبة
                                <span class="text-gray-500 text-xs">(اختياري)</span>
                            </label>
                            <select
                                name="difficulty_level"
                                id="difficulty_level"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                                <option value="">اختر مستوى الصعوبة</option>
                                <option value="beginner" {{ old('difficulty_level', $preferences->difficulty_level) == 'beginner' ? 'selected' : '' }}>
                                    مبتدئ - كتب سهلة وبسيطة
                                </option>
                                <option value="intermediate" {{ old('difficulty_level', $preferences->difficulty_level) == 'intermediate' ? 'selected' : '' }}>
                                    متوسط - كتب متوسطة التعقيد
                                </option>
                                <option value="advanced" {{ old('difficulty_level', $preferences->difficulty_level) == 'advanced' ? 'selected' : '' }}>
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
                            <input
                                type="text"
                                name="price_range"
                                id="price_range"
                                value="{{ old('price_range', $preferences->price_range) }}"
                                placeholder="مثال: 0-50، 50-100، 100+"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            @error('price_range')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4 pt-4">
                            <button
                                type="submit"
                                class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md">
                                تحديث التفضيلات ✨
                            </button>
                            <a
                                href="{{ route('dashboard') }}"
                                class="px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                                إلغاء
                            </a>
                        </div>

                        <!-- Generate Recommendations Button -->
                        <div class="pt-4 border-t border-gray-200">
                            <a
                                href="{{ route('recommendations.generate') }}"
                                class="block w-full bg-gradient-to-r from-green-500 to-green-600 text-white text-center font-semibold py-3 px-6 rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md">
                                🤖 احصل على توصيات جديدة بناءً على تفضيلاتك
                            </a>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-500 ml-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900 mb-1">نصيحة</h4>
                                    <p class="text-sm text-blue-800">
                                        بعد تحديث تفضيلاتك، يمكنك الحصول على توصيات جديدة تتناسب مع اهتماماتك المحدثة.
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