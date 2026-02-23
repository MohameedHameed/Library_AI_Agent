<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 p-2 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.usage_logs') }}</h1>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.usage-logs') }}"
            class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label
                        class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.api_source_filter') }}</label>
                    <select name="api_source"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">{{ __('messages.all_apis') }}</option>
                        <option value="openlibrary" {{ request('api_source') === 'openlibrary' ? 'selected' : '' }}>
                            OpenLibrary</option>
                        <option value="gutenberg" {{ request('api_source') === 'gutenberg' ? 'selected' : '' }}>
                            Gutenberg</option>
                        <option value="google_books" {{ request('api_source') === 'google_books' ? 'selected' : '' }}>
                            Google Books</option>
                    </select>
                </div>
                <div>
                    <label
                        class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.status_filter') }}</label>
                    <select name="status"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">{{ __('messages.all_statuses') }}</option>
                        <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>
                            {{ __('messages.success_status') }}</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>
                            {{ __('messages.failed_status') }}</option>
                    </select>
                </div>
                <div>
                    <label
                        class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.user_filter') }}</label>
                    <select name="user_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">{{ __('messages.all_users') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.date_from') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('messages.date_to') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit"
                    class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-xl hover:opacity-90 transition-opacity">
                    {{ __('messages.apply_filters') }}
                </button>
                <a href="{{ route('admin.usage-logs') }}"
                    class="text-sm text-gray-500 hover:text-gray-700 px-4 py-2 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                    {{ __('messages.reset_filters') }}
                </a>
            </div>
        </form>

        {{-- Results Count --}}
        <p class="text-sm text-gray-500">
            {{ __('messages.showing_entries') }}
            <strong>{{ $logs->firstItem() }}–{{ $logs->lastItem() }}</strong>
            {{ __('messages.of_entries') }}
            <strong>{{ $logs->total() }}</strong>
            {{ __('messages.log_entries') }}
        </p>

        {{-- Logs Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_id') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_status') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_api') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_query') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_user') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_results') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_time_ms') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($logs as $log)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-gray-400">{{ $log->id }}</td>
                                <td class="px-5 py-3">
                                    @if ($log->success)
                                        <span
                                            class="inline-flex items-center gap-1 bg-green-100 text-green-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            {{ __('messages.success_status') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 bg-red-100 text-red-700 text-xs font-medium px-2.5 py-1 rounded-full"
                                            title="{{ $log->error_message }}">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                            {{ __('messages.failed_status') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <span
                                        class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                        {{ $log->apiLabel() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 max-w-xs truncate text-gray-700" title="{{ $log->query }}">
                                    {{ $log->query ?? '-' }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ $log->user?->name ?? __('messages.guest') }}
                                </td>
                                <td class="px-5 py-3 text-gray-600">{{ number_format($log->results_count) }}</td>
                                <td class="px-5 py-3 text-gray-600">{{ $log->response_time_ms ?? '-' }}</td>
                                <td class="px-5 py-3 text-gray-400 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-gray-400 py-12">
                                    {{ __('messages.no_logs_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div>{{ $logs->links() }}</div>

        <a href="{{ route('admin.dashboard') }}"
            class="text-sm text-indigo-600 hover:underline">{{ __('messages.back_to_dashboard') }}</a>
    </div>
</x-app-layout>
