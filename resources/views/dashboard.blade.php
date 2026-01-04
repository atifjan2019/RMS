@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Total Reviews</p>
                    <p class="text-3xl font-bold text-white">{{ $stats['totalReviews'] }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-400/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Needs Reply</p>
                    <p class="text-3xl font-bold text-white">{{ $stats['unrepliedCount'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-400/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            @if($stats['unrepliedCount'] > 0)
                <a href="{{ route('reviews.index', ['status' => 'unreplied']) }}" class="text-amber-400 text-sm hover:underline mt-2 inline-block">
                    View unreplied →
                </a>
            @endif
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Average Rating</p>
                    <div class="flex items-center">
                        <p class="text-3xl font-bold text-white mr-2">{{ $stats['avgRating'] }}</p>
                        <x-star-rating :rating="$stats['avgRating']" />
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-400/10 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Setup Checklist (for new users) -->
    @if(!$hasGoogleConnection || !$activeLocation)
    <x-card>
        <h3 class="text-lg font-semibold text-white mb-4">Getting Started</h3>
        <div class="space-y-3">
            <div class="flex items-center {{ $isSubscribed ? 'text-green-400' : 'text-slate-400' }}">
                @if($isSubscribed)
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
                <span>Subscribe to activate your account</span>
            </div>

            <div class="flex items-center {{ $hasGoogleConnection ? 'text-green-400' : 'text-slate-400' }}">
                @if($hasGoogleConnection)
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
                <span>Connect your Google Business Profile</span>
                @if(!$hasGoogleConnection)
                    <a href="{{ route('google.index') }}" class="ml-auto text-amber-400 hover:underline text-sm">Connect →</a>
                @endif
            </div>

            <div class="flex items-center {{ $activeLocation ? 'text-green-400' : 'text-slate-400' }}">
                @if($activeLocation)
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
                <span>Select a location to manage</span>
                @if(!$activeLocation && $hasGoogleConnection)
                    <a href="{{ route('locations.index') }}" class="ml-auto text-amber-400 hover:underline text-sm">Select →</a>
                @endif
            </div>
        </div>
    </x-card>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Rating Distribution -->
        <x-card>
            <h3 class="text-lg font-semibold text-white mb-4">Rating Distribution</h3>
            <div class="space-y-3">
                @foreach($stats['ratingDistribution'] as $rating => $count)
                    @php
                        $percentage = $stats['totalReviews'] > 0 ? ($count / $stats['totalReviews']) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="flex items-center w-12">
                            <span class="text-white">{{ $rating }}</span>
                            <svg class="w-4 h-4 text-amber-400 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div class="flex-1 h-3 bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-slate-400 text-sm w-12 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>

        <!-- Recent Reviews -->
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Recent Reviews</h3>
                <a href="{{ route('reviews.index') }}" class="text-amber-400 text-sm hover:underline">View all →</a>
            </div>
            <div class="space-y-4">
                @forelse($stats['recentReviews'] as $review)
                    <div class="flex items-start gap-3 pb-4 border-b border-slate-700 last:border-0 last:pb-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-slate-900 font-semibold text-sm flex-shrink-0">
                            {{ substr($review->reviewer_name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-white text-sm">{{ $review->reviewer_name }}</span>
                                <x-star-rating :rating="$review->rating" size="sm" />
                                @if($review->status === 'unreplied')
                                    <x-badge type="warning">Needs Reply</x-badge>
                                @else
                                    <x-badge type="success">Replied</x-badge>
                                @endif
                            </div>
                            <p class="text-slate-400 text-sm truncate">{{ $review->comment ?? 'No comment' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-center py-4">No reviews yet</p>
                @endforelse
            </div>
        </x-card>
    </div>
</div>
@endsection
