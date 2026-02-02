<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                توصيات الكتب المخصصة لك
            </h2>
            <a
                href="{{ route('recommendations.generate') }}"
                class="bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold py-2 px-6 rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md text-sm">
                🤖 احصل على توصيات جديدة
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 ml-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4 animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-500 ml-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <!-- Empty State -->
            @if($recommendations->isEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">لا توجد توصيات بعد</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        احصل على توصيات كتب مخصصة بناءً على تفضيلاتك القرائية. سنساعدك في اكتشاف كتب رائعة تناسب اهتماماتك!
                    </p>
                    <div class="flex gap-4 justify-center">
                        <a
                            href="{{ route('recommendations.generate') }}"
                            class="bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold py-3 px-8 rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-[1.02] shadow-md">
                            🎯 احصل على توصيات الآن
                        </a>
                        <a
                            href="{{ route('preferences.edit') }}"
                            class="bg-white border-2 border-gray-300 text-gray-700 font-semibold py-3 px-8 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                            ⚙️ تعديل التفضيلات
                        </a>
                    </div>
                </div>
            </div>
            @else
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">إجمالي التوصيات</p>
                            <p class="text-3xl font-bold mt-1">{{ $recommendations->count() }}</p>
                        </div>
                        <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">توصيات الذكاء الاصطناعي</p>
                            <p class="text-3xl font-bold mt-1">{{ $recommendations->where('source', 'ai_recommendation')->count() }}</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 7H7v6h6V7z" />
                                <path fill-rule="evenodd" d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">كتب محفوظة</p>
                            <p class="text-3xl font-bold mt-1">{{ $recommendations->where('source', 'user_saved')->count() }}</p>
                        </div>
                        <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Books Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recommendations as $recommendation)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                    <!-- Book Cover -->
                    <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                        @if($recommendation->book_data['cover_image'] ?? null)
                        <img
                            src="{{ $recommendation->book_data['cover_image'] }}"
                            alt="{{ $recommendation->book_data['title'] ?? 'Book cover' }}"
                            class="w-full h-full object-cover"
                            onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 300%22%3E%3Crect fill=%22%23e5e7eb%22 width=%22200%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2220%22 fill=%22%239ca3af%22%3ENo Cover%3C/text%3E%3C/svg%3E';">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                            </svg>
                        </div>
                        @endif

                        <!-- Source Badge -->
                        <div class="absolute top-2 left-2">
                            @if($recommendation->book_data['source'] ?? null)
                            <span class="bg-white bg-opacity-90 text-xs font-semibold px-2 py-1 rounded-full shadow-sm">
                                {{ $recommendation->book_data['source'] }}
                            </span>
                            @endif
                        </div>

                        <!-- Score Badge -->
                        @if($recommendation->score)
                        <div class="absolute top-2 right-2">
                            <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-2 py-1 rounded-full shadow-sm">
                                ⭐ {{ $recommendation->score }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <!-- Book Info -->
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2 min-h-[3.5rem]">
                            {{ $recommendation->book_data['title'] ?? 'عنوان غير متوفر' }}
                        </h3>

                        <p class="text-sm text-gray-600 mb-3 line-clamp-1">
                            <span class="font-medium">المؤلف:</span>
                            {{ $recommendation->book_data['author'] ?? 'غير معروف' }}
                        </p>

                        @if(!empty($recommendation->book_data['description']))
                        <p class="text-sm text-gray-500 mb-4 line-clamp-3 flex-1">
                            {{ $recommendation->book_data['description'] }}
                        </p>
                        @endif

                        <!-- Book Meta -->
                        <div class="flex flex-wrap gap-2 mb-4 text-xs">
                            @if($recommendation->book_data['published_date'] ?? null)
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                📅 {{ $recommendation->book_data['published_date'] }}
                            </span>
                            @endif
                            @if($recommendation->book_data['page_count'] ?? null)
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                📄 {{ $recommendation->book_data['page_count'] }} صفحة
                            </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-auto">
                            @if($recommendation->book_data['preview_link'] ?? null)
                            <a
                                href="{{ $recommendation->book_data['preview_link'] }}"
                                target="_blank"
                                class="flex-1 bg-blue-500 text-white text-center text-sm font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">
                                عرض الكتاب
                            </a>
                            @endif

                            <form action="{{ route('recommendations.destroy', $recommendation->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('هل أنت متأكد من حذف هذا الكتاب من قائمتك؟')"
                                    class="bg-red-500 text-white text-sm font-semibold py-2 px-4 rounded-lg hover:bg-red-600 transition-colors"
                                    title="حذف">
                                    🗑️
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

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

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