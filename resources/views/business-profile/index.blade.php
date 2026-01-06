@extends('layouts.app')

@section('title', 'GBP Optimization')
@section('page_title', 'GBP Optimization')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    @if(!$activeLocation)
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
    @elseif(!$location)
        <x-card>
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <h3 class="text-lg font-semibold text-white mb-2">Loading Profile Data...</h3>
                <p class="text-slate-400 mb-4">Fetching your business information from Google</p>
            </div>
        </x-card>
    @else
        <!-- Edit Profile Form -->
        <x-card>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-white">Business Profile Details</h3>
                    <p class="text-sm text-slate-400 mt-1">View your Google Business Profile information</p>
                </div>
                
                <!-- Refresh Button -->
                <form action="{{ route('business-profile.refresh-data') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white font-medium transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Refresh from Google
                    </button>
                </form>
            </div>
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                    <p class="text-sm text-green-200">{{ session('success') }}</p>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                    <p class="text-sm text-red-200">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Info Message -->
            <div class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm text-blue-200 font-medium">Direct Google Management Recommended</p>
                        <p class="text-sm text-blue-300/80 mt-1">To make changes to your business profile, please visit <a href="https://business.google.com" target="_blank" class="underline hover:text-blue-200">Google Business Profile</a> directly. Some profile fields require verification through Google's interface.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">

                <!-- Business Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Business Name</label>
                    <input 
                        type="text" 
                        value="{{ $location['title'] ?? '' }}"
                        readonly
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-300 cursor-not-allowed"
                    >
                </div>

                <!-- Phone Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Phone Number</label>
                    <input 
                        type="text" 
                        value="{{ $location['phoneNumbers']['primaryPhone'] ?? '' }}"
                        readonly
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-300 cursor-not-allowed"
                    >
                </div>

                <!-- Website URL -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Website URL</label>
                    <input 
                        type="url" 
                        value="{{ $location['websiteUri'] ?? '' }}"
                        readonly
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-300 cursor-not-allowed"
                    >
                </div>

                <!-- Business Description -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Business Description
                    </label>
                    <textarea 
                        readonly
                        rows="5"
                        class="w-full px-4 py-3 bg-slate-900/50 border border-slate-700 rounded-lg text-slate-300 cursor-not-allowed resize-none"
                    >{{ $location['profile']['description'] ?? '' }}</textarea>
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
            </div>
        </x-card>

        <!-- Additional Profile Information -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Opening Hours -->
            @if(isset($location['regularHours']['periods']) && !empty($location['regularHours']['periods']))
            <x-card>
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Opening Hours
                </h3>
                <div class="space-y-2">
                    @php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $periods = collect($location['regularHours']['periods']);
                    @endphp
                    @foreach($days as $index => $day)
                        @php
                            $dayPeriods = $periods->where('openDay', strtoupper(substr($day, 0, 3)))->first();
                        @endphp
                        <div class="flex justify-between items-center py-2 border-b border-slate-800 last:border-0">
                            <span class="text-slate-300 font-medium">{{ $day }}</span>
                            @if($dayPeriods)
                                <span class="text-slate-400">{{ $dayPeriods['openTime'] ?? '00:00' }} - {{ $dayPeriods['closeTime'] ?? '24:00' }}</span>
                            @else
                                <span class="text-slate-500">Closed</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif

            <!-- Attributes -->
            @if(isset($location['attributes']) && !empty($location['attributes']))
            <x-card>
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Attributes
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(array_slice($location['attributes'], 0, 15) as $attribute)
                        <span class="px-3 py-1.5 bg-slate-700/50 text-slate-300 rounded-lg text-xs">
                            {{ $attribute['displayName'] ?? $attribute['name'] ?? 'Attribute' }}
                        </span>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>

        <!-- Services -->
        @if(isset($location['serviceItems']) && !empty($location['serviceItems']))
        <x-card>
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Services Offered
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($location['serviceItems'] as $service)
                    <div class="p-4 bg-slate-800/50 rounded-lg border border-slate-700">
                        <h4 class="font-medium text-white mb-1">{{ $service['structuredServiceItem']['displayName'] ?? $service['displayName'] ?? 'Service' }}</h4>
                        @if(isset($service['structuredServiceItem']['description']))
                            <p class="text-sm text-slate-400">{{ $service['structuredServiceItem']['description'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-card>
        @endif

        <!-- Additional Information -->
        @if(isset($location['moreHours']) || isset($location['serviceArea']) || isset($location['labels']))
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- More Hours -->
            @if(isset($location['moreHours']) && !empty($location['moreHours']))
            <x-card>
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Special Hours
                </h3>
                <div class="space-y-2">
                    @foreach($location['moreHours'] as $moreHour)
                        <div class="text-sm">
                            <p class="font-medium text-white">{{ $moreHour['hoursTypeId'] ?? 'Special' }}</p>
                        </div>
                    @endforeach
                </div>
            </x-card>
            @endif

            <!-- Service Area -->
            @if(isset($location['serviceArea']))
            <x-card>
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Service Area
                </h3>
                <p class="text-sm text-slate-300">Business serves customers in specific areas</p>
            </x-card>
            @endif

            <!-- Labels -->
            @if(isset($location['labels']) && !empty($location['labels']))
            <x-card>
                <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    Labels
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($location['labels'] as $label)
                        <span class="px-2 py-1 bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded text-xs">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </x-card>
            @endif
        </div>
        @endif

        <!-- Debug: Show All Available Data -->
        <x-card>
            <div x-data="{ showDebug: false }">
                <button @click="showDebug = !showDebug" class="flex items-center gap-2 text-slate-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showDebug">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showDebug">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span x-text="showDebug ? 'Hide Raw Data' : 'Show All Available Data'"></span>
                </button>
                
                <div x-show="showDebug" class="mt-4 p-4 bg-slate-900 rounded-lg overflow-auto max-h-96">
                    <pre class="text-xs text-slate-300">{{ json_encode($location, JSON_PRETTY_PRINT) }}</pre>
                </div>
            </div>
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
