@extends('layouts.app')

@section('title', 'Locations')
@section('page_title', 'Locations')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <p class="text-slate-400">Select a location to manage its reviews.</p>
        <form action="{{ route('locations.sync') }}" method="POST">
            @csrf
            <button type="submit" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Sync Locations
            </button>
        </form>
    </div>

    <!-- Locations Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($locations as $location)
            <x-card class="relative {{ $location->location_name === $activeLocationName ? 'ring-2 ring-amber-400' : '' }}">
                @if($location->location_name === $activeLocationName)
                    <div class="absolute top-4 right-4">
                        <x-badge type="success">Active</x-badge>
                    </div>
                @endif

                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-white mb-1">{{ $location->title }}</h3>
                    @if($location->primary_category)
                        <p class="text-amber-400 text-sm">{{ $location->primary_category }}</p>
                    @endif
                </div>

                <div class="space-y-2 text-sm text-slate-400 mb-6">
                    @if($location->full_address)
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span>{{ $location->full_address }}</span>
                        </div>
                    @endif

                    @if($location->phone)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $location->phone }}</span>
                        </div>
                    @endif

                    @if($location->website)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                            </svg>
                            <a href="{{ $location->website }}" target="_blank" class="text-amber-400 hover:underline truncate">
                                {{ parse_url($location->website, PHP_URL_HOST) }}
                            </a>
                        </div>
                    @endif
                </div>

                @if($location->location_name !== $activeLocationName)
                    <form action="{{ route('locations.set-active', $location) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Set as Active
                        </button>
                    </form>
                @else
                    <div class="flex gap-2">
                        <a href="{{ route('reviews.index') }}" class="btn-primary flex-1 text-center">
                            View Reviews
                        </a>
                        <form action="{{ route('locations.sync-reviews') }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="btn-secondary w-full">
                                Sync Reviews
                            </button>
                        </form>
                    </div>
                @endif
            </x-card>
        @empty
            <div class="col-span-full">
                <x-card class="text-center py-12">
                    <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-white mb-2">No Locations Found</h3>
                    <p class="text-slate-400 mb-4">
                        Make sure your Google account has access to a Business Profile.
                    </p>
                    <form action="{{ route('locations.sync') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Sync Locations
                        </button>
                    </form>
                </x-card>
            </div>
        @endforelse
    </div>
</div>
@endsection
