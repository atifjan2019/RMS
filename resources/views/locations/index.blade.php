@extends('layouts.app')

@section('title', 'Locations')
@section('page_title', 'Locations')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">Manage
                    Locations</h2>
                <p class="text-slate-400 mt-1">Select a location to manage its reviews and settings.</p>
            </div>
            <form action="{{ route('locations.sync') }}" method="POST">
                @csrf
                <button type="submit"
                    class="group relative px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl border border-slate-700 hover:border-amber-500/50 transition-all duration-300 flex items-center gap-2 overflow-hidden">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-amber-500/0 via-amber-500/5 to-amber-500/0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 transform translate-x-[-100%] group-hover:translate-x-[100%]">
                    </div>
                    <svg class="w-4 h-4 group-hover:rotate-180 transition-transform duration-500" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Sync with Google
                </button>
            </form>
        </div>

        <!-- Locations Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($locations as $location)
                <div class="relative group h-full">
                    <!-- Active State Glow -->
                    @if($location->location_name === $activeLocationName)
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl opacity-50 blur opacity-75 transition duration-1000 group-hover:duration-200">
                        </div>
                    @endif

                    <div
                        class="relative h-full bg-slate-800/80 backdrop-blur-sm p-6 rounded-xl border {{ $location->location_name === $activeLocationName ? 'border-amber-500/50' : 'border-slate-700 hover:border-slate-600' }} transition-all duration-300 flex flex-col">

                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1 min-w-0 pr-4">
                                <h3 class="text-lg font-bold text-white truncate mb-1" title="{{ $location->title }}">
                                    {{ $location->title }}</h3>
                                @if($location->primary_category)
                                    <div
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-700 text-slate-300 border border-slate-600">
                                        {{ $location->primary_category }}
                                    </div>
                                @endif
                            </div>
                            @if($location->location_name === $activeLocationName)
                                <span class="flex h-3 w-3 relative">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                                </span>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="space-y-3 text-sm text-slate-400 mb-6 flex-1">
                            @if($location->full_address)
                                <div class="flex items-start gap-3 group-hover:text-slate-300 transition-colors">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center flex-shrink-0 border border-slate-700 group-hover:border-slate-600">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span class="pt-1.5 leading-snug">{{ $location->full_address }}</span>
                                </div>
                            @endif

                            @if($location->phone)
                                <div class="flex items-center gap-3 group-hover:text-slate-300 transition-colors">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center flex-shrink-0 border border-slate-700 group-hover:border-slate-600">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <span class="pt-0.5">{{ $location->phone }}</span>
                                </div>
                            @endif

                            @if($location->website)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center flex-shrink-0 border border-slate-700 group-hover:border-slate-600">
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                        </svg>
                                    </div>
                                    <a href="{{ $location->website }}" target="_blank"
                                        class="pt-0.5 text-amber-500 hover:text-amber-400 hover:underline truncate transition-colors">
                                        {{ parse_url($location->website, PHP_URL_HOST) }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="pt-4 border-t border-slate-700/50 mt-auto">
                            @if($location->location_name !== $activeLocationName)
                                <form action="{{ route('locations.set-active', $location) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center justify-center px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-all duration-200 group-hover:shadow-lg">
                                        Set as Active
                                    </button>
                                </form>
                            @else
                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('reviews.index') }}"
                                        class="flex items-center justify-center px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-lg font-bold transition-all duration-200 shadow-lg shadow-amber-500/20">
                                        Dashboard
                                    </a>
                                    <form action="{{ route('locations.sync-reviews') }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full h-full flex items-center justify-center px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-all duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                            Sync
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div
                        class="relative overflow-hidden rounded-2xl border border-slate-700 bg-slate-800/50 backdrop-blur-xl p-12 text-center">
                        <div
                            class="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -mt-32">
                        </div>

                        <div class="relative z-10">
                            <div
                                class="w-20 h-20 bg-slate-800 rounded-2xl border border-slate-700 flex items-center justify-center mx-auto mb-6 transform rotate-3">
                                <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                            <h3 class="text-xl font-bold text-white mb-2">No Locations Found</h3>
                            <p class="text-slate-400 max-w-md mx-auto mb-8">
                                It looks like you haven't synced any locations from your Google Business Profile yet. Sync now
                                to get started.
                            </p>

                            <form action="{{ route('locations.sync') }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="btn-primary flex items-center px-8 py-3 text-base">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Sync Locations Now
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection