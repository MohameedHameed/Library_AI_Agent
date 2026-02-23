<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 p-2 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.api_settings') }}</h1>
        </div>
    </x-slot>

    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        {{-- Flash Messages --}}
        @if (session('success'))
            <div
                class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-2xl">
                <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Info Banner --}}
        <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-2xl p-4 text-sm text-indigo-700">
            <strong>{{ __('messages.how_it_works') }}:</strong> {{ __('messages.api_settings_info') }}
        </div>

        {{-- API Settings Cards --}}
        <div class="space-y-5">
            @forelse ($settings as $setting)
                @php
                    $statusKey = match ($setting->status) {
                        'approved' => 'approved',
                        'disabled' => 'disabled',
                        default => 'pending',
                    };
                    $statusColor = match ($setting->status) {
                        'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
                        'disabled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'dot' => 'bg-red-500'],
                        default => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
                    };
                @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-4 mb-5">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-bold text-gray-800">{{ $setting->display_name }}</h3>
                                    <span
                                        class="inline-flex items-center gap-1.5 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} text-xs font-bold px-3 py-1 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }}"></span>
                                        {{ __('messages.' . $statusKey) }}
                                    </span>
                                </div>
                                @if ($setting->api_url)
                                    <p class="text-sm text-gray-400">{{ $setting->api_url }}</p>
                                @endif
                                @if ($setting->approved_at && $setting->approvedBy)
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ __('messages.approved_by') }} {{ $setting->approvedBy->name }}
                                        {{ __('messages.on_date') }} {{ $setting->approved_at->format('d M Y') }}
                                    </p>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-2 shrink-0 flex-wrap">
                                @if (!$setting->isApproved())
                                    <form method="POST" action="{{ route('admin.api-settings.approve', $setting) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                                            {{ __('messages.approve') }}
                                        </button>
                                    </form>
                                @endif
                                @if (!$setting->isDisabled())
                                    <form method="POST" action="{{ route('admin.api-settings.disable', $setting) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="bg-red-50 hover:bg-red-100 text-red-700 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                                            {{ __('messages.disable') }}
                                        </button>
                                    </form>
                                @endif
                                @if (!$setting->isPending())
                                    <form method="POST" action="{{ route('admin.api-settings.pending', $setting) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="bg-gray-50 hover:bg-gray-100 text-gray-600 text-sm font-medium px-4 py-2 rounded-xl transition-colors">
                                            {{ __('messages.set_pending') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Edit Form --}}
                        <details class="group">
                            <summary
                                class="cursor-pointer text-sm text-indigo-600 font-medium hover:text-indigo-800 transition-colors list-none flex items-center gap-1">
                                <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                {{ __('messages.edit_api_key_settings') }}
                            </summary>
                            <form method="POST" action="{{ route('admin.api-settings.update', $setting) }}"
                                class="mt-4 space-y-4">
                                @csrf @method('PATCH')
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.api_key_label') }}</label>
                                    <input type="password" name="api_key"
                                        placeholder="{{ __('messages.api_key_placeholder') }}"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-shadow">
                                    @if ($setting->api_key)
                                        <p class="text-xs text-green-600 mt-1">{{ __('messages.api_key_saved') }}</p>
                                    @else
                                        <p class="text-xs text-gray-400 mt-1">{{ __('messages.api_key_empty') }}</p>
                                    @endif
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.api_url_label') }}</label>
                                    <input type="url" name="api_url" value="{{ $setting->api_url }}"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-shadow">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.admin_notes_label') }}</label>
                                    <textarea name="notes" rows="2"
                                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-shadow resize-none">{{ $setting->notes }}</textarea>
                                </div>
                                <button type="submit"
                                    class="bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-medium px-6 py-2.5 rounded-xl hover:opacity-90 transition-opacity">
                                    {{ __('messages.save_changes') }}
                                </button>
                            </form>
                        </details>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-2xl border border-gray-100">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    <p class="text-gray-400">{{ __('messages.no_api_settings') }}</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.dashboard') }}"
                class="text-sm text-indigo-600 hover:underline">{{ __('messages.back_to_dashboard') }}</a>
        </div>
    </div>
</x-app-layout>
