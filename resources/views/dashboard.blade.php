<x-app-layout>
    {{-- Modern Hero Section with Gradient Background --}}
    <div
        class="relative min-h-[calc(100vh-4rem)] bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 overflow-hidden">
        {{-- Animated Background Elements --}}
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"
                style="animation-delay: 1s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/5 rounded-full blur-2xl animate-pulse"
                style="animation-delay: 2s;"></div>
        </div>

        {{-- Main Content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            {{-- Welcome Header --}}
            <div class="text-center mb-12 animate-fade-in-down">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-4 drop-shadow-lg">
                    {{ __('messages.dashboard') }}
                </h1>
                <p class="text-xl md:text-2xl text-white/90 font-light">
                    اكتشف عالماً من الكتب المصممة خصيصاً لك
                </p>
            </div>

            {{-- Search Card with Glassmorphism --}}
            <div class="max-w-4xl mx-auto mb-12 animate-fade-in-up">
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl shadow-2xl p-8 md:p-12">
                    <form action="{{ route('books.search') }}" method="GET">
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1 relative group">
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                    <svg class="w-6 h-6 text-white/60 group-focus-within:text-white transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="query"
                                    placeholder="{{ __('messages.search_placeholder') }}"
                                    class="w-full bg-white/20 backdrop-blur-sm border-2 border-white/30 rounded-2xl px-6 py-4 pr-14 text-white placeholder-white/60 focus:outline-none focus:ring-4 focus:ring-white/30 focus:border-white/50 transition-all text-lg"
                                    required>
                            </div>
                            <button type="submit"
                                class="bg-white text-purple-600 font-bold px-8 py-4 rounded-2xl hover:bg-purple-50 focus:outline-none focus:ring-4 focus:ring-white/30 transition-all transform hover:scale-105 active:scale-95 shadow-xl text-lg whitespace-nowrap">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    {{ __('messages.search_button') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Quick Action Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto animate-fade-in-up"
                style="animation-delay: 0.2s;">
                {{-- My Recommendations Card --}}
                <a href="{{ route('recommendations.index') }}" class="group">
                    <div
                        class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="bg-gradient-to-br from-yellow-400 to-orange-500 p-3 rounded-xl group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ __('messages.my_recommendations') }}</h3>
                        </div>
                        <p class="text-white/80 text-sm">عرض توصياتي المخصصة</p>
                    </div>
                </a>

                {{-- My Preferences Card --}}
                <a href="{{ route('preferences.edit') }}" class="group">
                    <div
                        class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="bg-gradient-to-br from-blue-400 to-cyan-500 p-3 rounded-xl group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">{{ __('messages.my_preferences') }}</h3>
                        </div>
                        <p class="text-white/80 text-sm">تحديث تفضيلاتي القرائية</p>
                    </div>
                </a>

                {{-- Get New Recommendations Card --}}
                <a href="{{ route('recommendations.generate') }}" class="group">
                    <div
                        class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl p-6 hover:bg-white/20 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <div class="flex items-center gap-4 mb-3">
                            <div
                                class="bg-gradient-to-br from-green-400 to-emerald-500 p-3 rounded-xl group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 7H7v6h6V7z" />
                                    <path fill-rule="evenodd"
                                        d="M7 2a1 1 0 012 0v1h2V2a1 1 0 112 0v1h2a2 2 0 012 2v2h1a1 1 0 110 2h-1v2h1a1 1 0 110 2h-1v2a2 2 0 01-2 2h-2v1a1 1 0 11-2 0v-1H9v1a1 1 0 11-2 0v-1H5a2 2 0 01-2-2v-2H2a1 1 0 110-2h1V9H2a1 1 0 010-2h1V5a2 2 0 012-2h2V2zM5 5h10v10H5V5z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-white">توصيات جديدة</h3>
                        </div>
                        <p class="text-white/80 text-sm">احصل على اقتراحات جديدة</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fade-in-down 0.8s ease-out;
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.8s ease-out;
        }
    </style>
</x-app-layout>
