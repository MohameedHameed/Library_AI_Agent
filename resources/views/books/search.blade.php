<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                نتائج البحث: "{{ $query }}"
            </h2>
            <a href="{{ route('dashboard') }}" class="text-blue-500 hover:text-blue-600 font-semibold text-sm">
                ← العودة للبحث
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search Box -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form action="{{ route('books.search') }}" method="GET">
                    <div class="flex gap-4">
                        <input type="text" name="search" value="{{ $query }}"
                            placeholder="ابحث عن كتاب آخر..."
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            required>
                        <button type="submit"
                            class="bg-blue-500 text-white font-semibold py-3 px-8 rounded-lg hover:bg-blue-600 transition-colors">
                            بحث
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Count -->
            @if (!empty($books))
                <div class="mb-6">
                    <p class="text-gray-600">
                        تم العثور على <span class="font-bold text-gray-900">{{ count($books) }}</span> نتيجة
                    </p>
                </div>
            @endif

            <!-- Empty State -->
            @if (empty($books))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <div class="mb-6">
                            <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">لم يتم العثور على نتائج</h3>
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            لم نتمكن من العثور على كتب تطابق بحثك "{{ $query }}". جرب كلمات بحث مختلفة أو أقل
                            تحديداً.
                        </p>
                        <a href="{{ route('dashboard') }}"
                            class="inline-block bg-blue-500 text-white font-semibold py-3 px-8 rounded-lg hover:bg-blue-600 transition-colors">
                            بحث جديد
                        </a>
                    </div>
                </div>
            @else
                <!-- Books Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($books as $book)
                        <div
                            class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col group">
                            <!-- Book Cover -->
                            <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                @if ($book['cover_image'] ?? null)
                                    <img src="{{ $book['cover_image'] }}" alt="{{ $book['title'] ?? 'Book cover' }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22200%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%239ca3af%22%3ENo Cover%3C/text%3E%3C/svg%3E';">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                        </svg>
                                    </div>
                                @endif

                                <!-- Source Badge -->
                                <div class="absolute top-2 left-2">
                                    <span
                                        class="bg-white bg-opacity-90 text-xs font-semibold px-2 py-1 rounded-full shadow-sm">
                                        {{ $book['source'] ?? 'Unknown' }}
                                    </span>
                                </div>

                                <!-- Language Badge -->
                                @if ($book['language'] ?? null)
                                    <div class="absolute bottom-2 left-2">
                                        <span
                                            class="bg-black bg-opacity-60 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                            {{ strtoupper($book['language']) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Book Info -->
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">
                                    {{ $book['title'] ?? 'عنوان غير متوفر' }}
                                </h3>

                                <p class="text-sm text-gray-600 mb-3 line-clamp-1">
                                    <span class="font-medium">المؤلف:</span>
                                    {{ $book['author'] ?? 'غير معروف' }}
                                </p>

                                @if (!empty($book['description']))
                                    <p class="text-sm text-gray-500 mb-4 line-clamp-3 flex-1">
                                        {{ $book['description'] }}
                                    </p>
                                @endif

                                <!-- Book Meta -->
                                <div class="flex flex-wrap gap-2 mb-4 text-xs">
                                    @if ($book['published_date'] ?? null)
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            {{ $book['published_date'] }}
                                        </span>
                                    @endif
                                    @if ($book['page_count'] ?? null)
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            {{ $book['page_count'] }} صفحة
                                        </span>
                                    @endif
                                </div>

                                <!-- Categories -->
                                @if (!empty($book['categories']) && is_array($book['categories']))
                                    <div class="mb-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach (array_slice($book['categories'], 0, 3) as $category)
                                                <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded">
                                                    {{ $category }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex gap-2 mt-auto">
                                    @if ($book['preview_link'] ?? null)
                                        <a href="{{ $book['preview_link'] }}" target="_blank"
                                            class="flex-1 bg-blue-500 text-white text-center text-sm font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                                            عرض الكتاب
                                        </a>
                                    @endif

                                    <form action="{{ route('recommendations.store') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="book_api_id" value="{{ $book['api_id'] }}">
                                        <button type="submit"
                                            class="bg-green-500 text-white text-sm font-semibold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors whitespace-nowrap"
                                            title="حفظ في قائمتي">
                                            حفظ
                                        </button>
                                    </form>
                                </div>

                                <!-- Download Links (for Gutenberg books) -->
                                @if (!empty($book['download_links']) && is_array($book['download_links']))
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-xs font-semibold text-gray-700 mb-2">تحميل مجاني:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @if (isset($book['download_links']['application/epub+zip']))
                                                <a href="{{ $book['download_links']['application/epub+zip'] }}"
                                                    target="_blank"
                                                    class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded hover:bg-purple-200">
                                                    EPUB
                                                </a>
                                            @endif
                                            @if (isset($book['download_links']['application/x-mobipocket-ebook']))
                                                <a href="{{ $book['download_links']['application/x-mobipocket-ebook'] }}"
                                                    target="_blank"
                                                    class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200">
                                                    MOBI
                                                </a>
                                            @endif
                                            @if (isset($book['download_links']['text/html']))
                                                <a href="{{ $book['download_links']['text/html'] }}" target="_blank"
                                                    class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">
                                                    HTML
                                                </a>
                                            @endif
                                            @if (isset($book['download_links']['text/plain']))
                                                <a href="{{ $book['download_links']['text/plain'] }}" target="_blank"
                                                    class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded hover:bg-gray-200">
                                                    TXT
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <style>
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
