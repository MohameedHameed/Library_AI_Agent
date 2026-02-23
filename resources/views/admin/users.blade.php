<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 p-2 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.users_management') }}</h1>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div
                class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
                {{ session('error') }}
            </div>
        @endif

        {{-- Users Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_name_email') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_role') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_api_calls') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_joined') }}</th>
                            <th
                                class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">
                                {{ __('messages.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-indigo-500 flex items-center justify-center text-white font-bold text-sm shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    @if ($user->isAdmin())
                                        <span
                                            class="inline-flex items-center gap-1 bg-violet-100 text-violet-700 text-xs font-bold px-3 py-1 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ __('messages.role_admin') }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1 rounded-full">
                                            {{ __('messages.role_user') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        class="bg-indigo-50 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full">
                                        {{ number_format($user->api_usage_logs_count) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-4">
                                    @if ($user->id !== auth()->id())
                                        @if ($user->isAdmin())
                                            <form method="POST" action="{{ route('admin.users.demote', $user) }}"
                                                class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs bg-red-50 hover:bg-red-100 text-red-700 font-medium px-3 py-1.5 rounded-xl transition-colors"
                                                    onclick="return confirm('{{ __('messages.confirm_demote', ['name' => $user->name]) }}')">
                                                    {{ __('messages.demote_to_user') }}
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.promote', $user) }}"
                                                class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs bg-violet-50 hover:bg-violet-100 text-violet-700 font-medium px-3 py-1.5 rounded-xl transition-colors"
                                                    onclick="return confirm('{{ __('messages.confirm_promote', ['name' => $user->name]) }}')">
                                                    {{ __('messages.promote_to_admin') }}
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span
                                            class="text-xs text-gray-400 italic">{{ __('messages.thats_you') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-gray-400 py-12">
                                    {{ __('messages.no_users_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div>{{ $users->links() }}</div>

        <a href="{{ route('admin.dashboard') }}"
            class="text-sm text-indigo-600 hover:underline">{{ __('messages.back_to_dashboard') }}</a>
    </div>
</x-app-layout>
