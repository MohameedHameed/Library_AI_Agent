<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.my_favorites') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('info'))
                <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                    {{ session('info') }}
                </div>
            @endif

            @if ($favoriteBooks->isEmpty())
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('messages.no_favorites') }}</h3>
                    <p class="mt-2 text-sm text-gray-500">{{ __('messages.start_adding_favorites') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('recommendations.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ __('messages.view_recommendations') }}
                        </a>
                    </div>
                </div>
            @else
                <!-- Favorites Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($favoriteBooks as $book)
                        <div
                            class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
                            <div class="p-6">
                                <!-- Book Cover -->
                                @if (!empty($book['cover_image']))
                                    <img src="{{ $book['cover_image'] }}" alt="{{ $book['title'] }}"
                                        class="w-full h-64 object-cover rounded-lg mb-4">
                                @else
                                    <div
                                        class="w-full h-64 bg-gradient-to-br from-blue-400 to-purple-500 rounded-lg mb-4 flex items-center justify-center">
                                        <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Book Info -->
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">{{ $book['title'] }}</h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    <span class="font-semibold">{{ __('messages.author') }}:</span>
                                    {{ $book['author'] ?? __('messages.unknown') }}
                                </p>

                                @if (!empty($book['published_date']))
                                    <p class="text-sm text-gray-600 mb-2">
                                        <span class="font-semibold">{{ __('messages.published') }}:</span>
                                        {{ $book['published_date'] }}
                                    </p>
                                @endif

                                @if (!empty($book['page_count']) && $book['page_count'] > 0)
                                    <p class="text-sm text-gray-600 mb-2">
                                        <span class="font-semibold">{{ __('messages.pages') }}:</span>
                                        {{ $book['page_count'] }}
                                    </p>
                                @endif

                                @if (!empty($book['description']))
                                    <p class="text-sm text-gray-700 mb-4 line-clamp-3">
                                        {{ Str::limit($book['description'], 150) }}</p>
                                @endif

                                <!-- Price -->
                                @if (isset($book['price']))
                                    <div class="mb-4">
                                        @if ($book['price'] == 0)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                {{ __('messages.free') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                                {{ $book['currency'] }} {{ number_format($book['price'], 2) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Actions -->
                                <div class="flex gap-2 mt-4">
                                    @if (!empty($book['preview_link']))
                                        <a href="{{ $book['preview_link'] }}" target="_blank"
                                            class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                            {{ __('messages.preview') }}
                                        </a>
                                    @endif

                                    <!-- Remove from Favorites -->
                                    <form action="{{ route('favorites.destroy', $book['favorite_id']) }}"
                                        method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('{{ __('messages.confirm_remove_favorite') }}')"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                            {{ __('messages.remove') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
