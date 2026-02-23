<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 p-2 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.admin_dashboard') }}</h1>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

        {{-- ─── Stats Cards ─── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $cards = [
                    [
                        'label' => __('messages.total_users'),
                        'value' => $stats['total_users'],
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                        'color' => 'from-blue-500 to-cyan-500',
                    ],
                    [
                        'label' => __('messages.api_calls_today'),
                        'value' => $stats['calls_today'],
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'color' => 'from-green-500 to-emerald-500',
                    ],
                    [
                        'label' => __('messages.this_week'),
                        'value' => $stats['calls_this_week'],
                        'icon' =>
                            'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'color' => 'from-purple-500 to-violet-500',
                    ],
                    [
                        'label' => __('messages.failed_calls'),
                        'value' => $stats['failed_calls'],
                        'icon' =>
                            'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                        'color' => 'from-red-500 to-rose-500',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div
                    class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="bg-gradient-to-br {{ $card['color'] }} p-3 rounded-xl shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $card['icon'] }}" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($card['value']) }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ─── API Status + Per-API Breakdown ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- API Status Summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block"></span>
                    {{ __('messages.api_config_status') }}
                </h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                        <span class="text-green-700 font-medium">{{ __('messages.approved_apis') }}</span>
                        <span
                            class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full">{{ $stats['approved_apis'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-xl">
                        <span class="text-yellow-700 font-medium">{{ __('messages.pending_approval') }}</span>
                        <span
                            class="bg-yellow-100 text-yellow-800 text-sm font-bold px-3 py-1 rounded-full">{{ $stats['pending_apis'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-xl">
                        <span class="text-red-700 font-medium">{{ __('messages.disabled_apis') }}</span>
                        <span
                            class="bg-red-100 text-red-800 text-sm font-bold px-3 py-1 rounded-full">{{ $stats['disabled_apis'] }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.api-settings') }}"
                    class="mt-4 block text-center bg-gradient-to-r from-violet-600 to-indigo-600 text-white py-2 rounded-xl font-medium hover:opacity-90 transition-opacity text-sm">
                    {{ __('messages.manage_api_settings') }}
                </a>
            </div>

            {{-- Per-API Breakdown --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                    {{ __('messages.api_usage_breakdown') }}
                </h2>
                @if ($apiBreakdown->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-6">{{ __('messages.no_api_calls_yet') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach ($apiBreakdown as $api)
                            @php
                                $labels = [
                                    'openlibrary' => 'OpenLibrary',
                                    'gutenberg' => 'Project Gutenberg',
                                    'google_books' => 'Google Books',
                                ];
                                $label = $labels[$api->api_source] ?? ucfirst($api->api_source);
                                $successRate = $api->total > 0 ? round(($api->successful / $api->total) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="font-medium text-gray-700">{{ $label }}</span>
                                        <span class="text-gray-500">
                                            {{ number_format($api->total) }} {{ __('messages.calls') }} ·
                                            {{ $successRate }}% {{ __('messages.success_rate') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full"
                                            style="width: {{ $successRate }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ─── Recent Logs + Top Users ─── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Recent Activity --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('messages.recent_api_calls') }}</h2>
                    <a href="{{ route('admin.usage-logs') }}"
                        class="text-sm text-indigo-600 hover:underline">{{ __('messages.view_all') }}</a>
                </div>
                @if ($recentLogs->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-6">{{ __('messages.no_logs_yet') }}</p>
                @else
                    <div class="space-y-2">
                        @foreach ($recentLogs as $log)
                            <div
                                class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                                <span
                                    class="w-2 h-2 rounded-full shrink-0 {{ $log->success ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">
                                        {{ $log->query ?? __('messages.no_query') }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->apiLabel() }} ·
                                        {{ $log->user?->name ?? __('messages.guest') }} ·
                                        {{ $log->created_at->diffForHumans() }}</p>
                                </div>
                                @if ($log->response_time_ms)
                                    <span class="text-xs text-gray-400 shrink-0">{{ $log->response_time_ms }}ms</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Top Users --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('messages.top_users_by_usage') }}</h2>
                    <a href="{{ route('admin.users') }}"
                        class="text-sm text-indigo-600 hover:underline">{{ __('messages.manage_users') }}</a>
                </div>
                @if ($topUsers->isEmpty())
                    <p class="text-gray-400 text-sm text-center py-6">{{ __('messages.no_users_yet') }}</p>
                @else
                    <div class="space-y-2">
                        @foreach ($topUsers as $i => $user)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50">
                                <span
                                    class="w-7 h-7 bg-gradient-to-br from-violet-500 to-indigo-500 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ $i + 1 }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span
                                    class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded-full shrink-0">
                                    {{ number_format($user->api_usage_logs_count) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
