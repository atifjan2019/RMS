@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')

@section('content')
<div class="space-y-6">
    <!-- Auto-Reply Settings -->
    <x-card>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold text-white">Auto-Reply Settings</h2>
                <p class="text-slate-400 text-sm mt-1">Automatically reply to reviews using AI</p>
            </div>
        </div>

        <form action="{{ route('settings.auto-reply') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Enable/Disable Toggle -->
            <div class="flex items-center justify-between p-4 bg-slate-800/50 rounded-xl">
                <div>
                    <h3 class="font-medium text-white">Enable Auto-Reply</h3>
                    <p class="text-slate-400 text-sm">When enabled, AI will automatically generate and post replies</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="auto_reply_enabled" 
                        value="1"
                        {{ $tenant->auto_reply_enabled ? 'checked' : '' }}
                        class="sr-only peer"
                    >
                    <div class="w-14 h-7 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-slate-400 after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500 peer-checked:after:bg-white"></div>
                </label>
            </div>

            <!-- Tone Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-3">Reply Tone</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($tones as $tone)
                        <label class="relative cursor-pointer">
                            <input 
                                type="radio" 
                                name="auto_reply_tone" 
                                value="{{ $tone }}"
                                {{ $tenant->auto_reply_tone === $tone ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="p-4 bg-slate-800 border-2 border-slate-700 rounded-xl peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                <div class="flex items-center gap-3">
                                    @if($tone === 'friendly')
                                        <span class="text-2xl">😊</span>
                                    @elseif($tone === 'professional')
                                        <span class="text-2xl">💼</span>
                                    @else
                                        <span class="text-2xl">🔧</span>
                                    @endif
                                    <div>
                                        <p class="font-medium text-white capitalize">{{ $tone }}</p>
                                        <p class="text-xs text-slate-400">
                                            @if($tone === 'friendly')
                                                Warm and personable
                                            @elseif($tone === 'professional')
                                                Polished and courteous
                                            @else
                                                Empathetic for negative reviews
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Star Rating Selection -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-3">Auto-reply to these ratings</label>
                <p class="text-slate-500 text-xs mb-4">Select which star ratings should receive automatic replies</p>
                <div class="flex flex-wrap gap-3">
                    @for($i = 5; $i >= 1; $i--)
                        <label class="relative cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="auto_reply_stars[]" 
                                value="{{ $i }}"
                                {{ in_array($i, $tenant->auto_reply_stars ?? []) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="flex items-center gap-2 px-4 py-3 bg-slate-800 border-2 border-slate-700 rounded-xl peer-checked:border-amber-500 peer-checked:bg-amber-500/10 transition-all">
                                <span class="font-medium text-white">{{ $i }}</span>
                                <div class="flex">
                                    @for($s = 1; $s <= $i; $s++)
                                        <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- Delay Setting -->
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-2">Reply Delay (minutes)</label>
                <p class="text-slate-500 text-xs mb-3">Wait this long before sending auto-reply (gives you time to review first)</p>
                <div class="flex items-center gap-4">
                    <input 
                        type="number" 
                        name="auto_reply_delay_minutes" 
                        value="{{ $tenant->auto_reply_delay_minutes ?? 5 }}"
                        min="1"
                        max="1440"
                        class="w-32 bg-slate-800 border border-slate-700 rounded-lg px-4 py-2 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                    >
                    <span class="text-slate-400 text-sm">minutes</span>
                </div>
            </div>

            <!-- Warning -->
            <div class="p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-amber-400 font-medium">Important</p>
                        <p class="text-slate-400 text-sm mt-1">
                            Auto-replies are posted directly to Google. We recommend starting with only 5-star reviews 
                            and reviewing the AI responses before enabling for lower ratings.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </x-card>
</div>
@endsection
