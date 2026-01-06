@extends('layouts.app')

@section('title', 'Settings')
@section('page_title', 'Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Auto-Reply Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">Auto-Reply Configuration</h2>
            <p class="text-slate-400 mt-1">Configure how AI automatically responds to your customer reviews</p>
        </div>
        
    </div>

    <!-- Main Settings Card -->
    <form action="{{ route('settings.auto-reply') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Master Switch -->
        <div class="relative overflow-hidden rounded-2xl border border-slate-700 bg-slate-800/50 backdrop-blur-xl p-8 transition-all duration-300 hover:border-amber-500/30">
            <div class="absolute top-0 right-0 -mt-16 -mr-16 w-32 h-32 bg-amber-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative flex items-center justify-between">
                <div class="space-y-1">
                    <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                        <span>Auto-Reply System</span>
                        @if($tenant->auto_reply_enabled)
                            <span class="px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 text-xs font-medium border border-green-500/20">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full bg-slate-500/10 text-slate-400 text-xs font-medium border border-slate-500/20">Inactive</span>
                        @endif
                    </h3>
                    <p class="text-slate-400 max-w-xl">When enabled, our AI will automatically generate and publish responses to new reviews based on your configuration below.</p>
                </div>
                
                <label class="relative inline-flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="auto_reply_enabled" 
                        value="1"
                        {{ $tenant->auto_reply_enabled ? 'checked' : '' }}
                        class="sr-only peer"
                    >
                    <div class="w-14 h-7 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500"></div>
                </label>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tone Selection -->
            <div class="rounded-2xl border border-slate-700 bg-slate-800/50 backdrop-blur-sm p-6 space-y-4">
                <h3 class="font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                    </svg>
                    Voice & Tone
                </h3>
                <p class="text-sm text-slate-400">Choose the personality the AI should use when responding.</p>
                
                <div class="grid grid-cols-1 gap-3">
                    @foreach($tones as $tone)
                        <label class="relative group cursor-pointer block" x-data="{ checked: {{ in_array($tone, $tenant->auto_reply_tone ?? []) ? 'true' : 'false' }} }">
                            <input 
                                type="checkbox" 
                                name="auto_reply_tone[]" 
                                value="{{ $tone }}"
                                {{ in_array($tone, $tenant->auto_reply_tone ?? []) ? 'checked' : '' }}
                                class="peer sr-only"
                                @change="checked = $el.checked"
                            >
                            <div class="flex items-center justify-between p-4 rounded-xl border-2 transition-all duration-200 peer-focus:ring-2 peer-focus:ring-amber-500 peer-focus:ring-offset-2 peer-focus:ring-offset-slate-900"
                                :class="checked ? 'border-amber-500 bg-amber-500/10 shadow-lg shadow-amber-500/10' : 'border-slate-700 bg-slate-900/50 hover:bg-slate-800'">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-800 border transition-transform text-xl group-hover:scale-110"
                                        :class="checked ? 'border-amber-500' : 'border-slate-700'">
                                        @if($tone === 'friendly') 😊
                                        @elseif($tone === 'professional') 💼
                                        @else 🔧
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-white capitalize">{{ $tone }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            @if($tone === 'friendly') Warm, approachable, and personable
                                            @elseif($tone === 'professional') Formal, polite, and business-focused
                                            @else Solution-oriented and empathetic
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <!-- Checkmark in Circle -->
                                <div class="relative w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                    :class="checked ? 'border-amber-500 bg-amber-500' : 'border-slate-600 bg-transparent'">
                                    <svg class="w-3.5 h-3.5 text-white transition-transform" 
                                        :class="checked ? 'scale-100' : 'scale-0'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Rules & Triggers -->
            <div class="space-y-6">
                <!-- Star Rating -->
                <div class="rounded-2xl border border-slate-700 bg-slate-800/50 backdrop-blur-sm p-6 space-y-4">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Target Ratings
                    </h3>
                    <p class="text-sm text-slate-400">Auto-reply only to reviews with these ratings.</p>
                    
                    <div class="flex flex-col rounded-xl overflow-hidden border border-slate-700">
                        @for($i = 5; $i >= 1; $i--)
                            <label class="relative cursor-pointer group block font-normal" x-data="{ checked: {{ in_array($i, $tenant->auto_reply_stars ?? []) ? 'true' : 'false' }} }">
                                <input 
                                    type="checkbox" 
                                    name="auto_reply_stars[]" 
                                    value="{{ $i }}"
                                    {{ in_array($i, $tenant->auto_reply_stars ?? []) ? 'checked' : '' }}
                                    class="peer sr-only"
                                    @change="checked = $el.checked"
                                >
                                <div class="flex items-center justify-between px-4 py-3 {{ $i > 1 ? 'border-b border-slate-700' : '' }} transition-all"
                                    :class="checked ? 'bg-amber-500/10' : 'bg-slate-900/50 hover:bg-slate-800'">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono text-slate-400 w-4">{{ $i }}</span>
                                        <div class="flex gap-0.5">
                                            @for($s = 1; $s <= 5; $s++)
                                                <svg class="w-4 h-4 {{ $s <= $i ? 'text-amber-400' : 'text-slate-700' }}" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>

                                    <!-- Custom Checkbox Indicator -->
                                    <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all"
                                        :class="checked ? 'border-amber-500 bg-amber-500' : 'border-slate-600'">
                                        <svg class="w-3.5 h-3.5 text-white transition-transform" 
                                            :class="checked ? 'scale-100' : 'scale-0'"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        @endfor
                    </div>
                </div>


                <!-- Timing -->
                <div class="rounded-2xl border border-slate-700 bg-slate-800/50 backdrop-blur-sm p-6 space-y-4">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Response Delay
                    </h3>
                    <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-700">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-slate-300">Wait time before posting (minutes)</label>
                        </div>
                        <input 
                            type="number" 
                            name="auto_reply_delay_minutes" 
                            value="{{ $tenant->auto_reply_delay_minutes ?? 5 }}"
                            min="1"
                            max="1440"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                            placeholder="Enter minutes (e.g. 30)"
                        >
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Allows window for manual review intervention
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warning/Info -->
        <div class="rounded-xl bg-gradient-to-r from-blue-500/10 to-indigo-500/10 border border-blue-500/20 p-4">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm">
                    <p class="font-medium text-blue-400 mb-1">Safety First</p>
                    <p class="text-blue-300/80">
                        We recommend starting with 5-star reviews only and a delay of at least 30 minutes. This gives you time to manually intervene if a customer leaves a complex review that needs human attention.
                    </p>
                </div>
            </div>
        </div>

        <!-- Sticky Footer -->
        <div class="sticky bottom-4 z-40">
            <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-xl rounded-xl -m-4"></div>
            <div class="relative flex items-center justify-end gap-4 p-4 border border-slate-700 bg-slate-800/80 rounded-xl shadow-2xl">
                <span class="text-slate-400 text-sm hidden sm:block">Changes are not saved automatically</span>
                <button type="submit" class="btn-primary min-w-[160px] shadow-lg shadow-amber-500/20">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
