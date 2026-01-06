@extends('layouts.app')

@section('title', 'GBP Optimization')
@section('page_title', 'GBP Optimization')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @if(!$location)
        <x-card>
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">No Active Location</h3>
                <p class="text-slate-400 mb-4">Please select a location to manage your business profile.</p>
                <a href="{{ route('locations.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-900 font-medium transition">
                    Select Location →
                </a>
            </div>
        </x-card>
    @else
        <!-- AI Recommendations -->
        @if($aiRecommendations)
        <div class="rounded-2xl border border-blue-500/30 bg-gradient-to-br from-blue-500/5 to-transparent backdrop-blur-sm p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
            
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">AI Profile Optimization</h3>
                            <p class="text-sm text-slate-400">Recommendations to improve your Google Business Profile</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('business-profile.refresh-recommendations') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-sm rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 hover:border-blue-500/50 transition-all flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Refresh
                        </button>
                    </form>
                </div>
                
                <div class="prose prose-invert prose-sm max-w-none prose-headings:text-white prose-headings:font-semibold prose-p:text-slate-300 prose-strong:text-blue-400 prose-ul:text-slate-300 prose-li:text-slate-300">
                    {!! \Illuminate\Support\Str::markdown($aiRecommendations) !!}
                </div>
            </div>
        </div>
        @endif

        <!-- Edit Profile Form -->
        <x-card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-white">GBP Score Optimization</h3>
                    <p class="text-sm text-slate-400 mt-1">Optimize your Google Business Profile for better visibility</p>
                </div>
            </div>

            <form action="{{ route('business-profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Business Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Business Name</label>
                    <input 
                        type="text" 
                        name="title" 
                        value="{{ old('title', $location['title'] ?? '') }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition"
                        placeholder="Your Business Name"
                    >
                    @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Phone Number</label>
                    <input 
                        type="text" 
                        name="phone_number" 
                        value="{{ old('phone_number', $location['phoneNumbers']['primaryPhone'] ?? '') }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition"
                        placeholder="+1 (555) 123-4567"
                    >
                    @error('phone_number')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website URL -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Website URL</label>
                    <input 
                        type="url" 
                        name="website_uri" 
                        value="{{ old('website_uri', $location['websiteUri'] ?? '') }}"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition"
                        placeholder="https://yourbusiness.com"
                    >
                    @error('website_uri')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Business Description -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Business Description
                        <span class="text-slate-500 font-normal">(Max 750 characters)</span>
                    </label>
                    <textarea 
                        name="description" 
                        rows="5"
                        maxlength="750"
                        class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-white placeholder-slate-500 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition resize-none"
                        placeholder="Tell customers what makes your business special..."
                    >{{ old('description', $location['profile']['description'] ?? '') }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500 mt-1">
                        <span id="char-count">{{ strlen($location['profile']['description'] ?? '') }}</span>/750 characters
                    </p>
                </div>

                <!-- Current Categories (Read-only) -->
                @if(isset($location['categories']) && !empty($location['categories']))
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Business Categories</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($location['categories'] as $category)
                            <span class="px-3 py-1.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-lg text-sm">
                                {{ $category['displayName'] ?? 'Unknown' }}
                            </span>
                        @endforeach
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Categories can be managed in Google Business Profile Manager</p>
                </div>
                @endif

                <!-- Save Button -->
                <div class="flex justify-end pt-4 border-t border-slate-700">
                    <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-slate-900 font-medium rounded-lg transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </x-card>
    @endif
</div>

<script>
    // Character counter for description
    const textarea = document.querySelector('textarea[name="description"]');
    const charCount = document.getElementById('char-count');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
</script>
@endsection
