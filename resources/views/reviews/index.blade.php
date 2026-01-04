@extends('layouts.app')

@section('title', 'Reviews')
@section('page_title', 'Reviews')

@section('content')
<div class="space-y-6" x-data="reviewsPage()">
    <!-- Stats Bar -->
    <div class="flex flex-wrap items-center gap-4 text-sm">
        <div class="flex items-center gap-2 text-slate-400">
            <span>Total:</span>
            <span class="text-white font-medium">{{ $stats['total'] }}</span>
        </div>
        <div class="flex items-center gap-2 text-slate-400">
            <span>Unreplied:</span>
            <span class="text-amber-400 font-medium">{{ $stats['unreplied'] }}</span>
        </div>
        <div class="flex items-center gap-2 text-slate-400">
            <span>Avg Rating:</span>
            <span class="text-white font-medium">{{ $stats['average_rating'] }}</span>
            <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
        </div>

        <div class="ml-auto">
            <form action="{{ route('locations.sync-reviews') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn-secondary text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Sync Now
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <x-card>
        <form method="GET" action="{{ route('reviews.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-input
                    type="text"
                    name="search"
                    placeholder="Search reviews..."
                    :value="$filters['search'] ?? ''"
                />
            </div>

            <x-select name="status">
                <option value="">All Status</option>
                <option value="unreplied" {{ ($filters['status'] ?? '') === 'unreplied' ? 'selected' : '' }}>Unreplied</option>
                <option value="replied" {{ ($filters['status'] ?? '') === 'replied' ? 'selected' : '' }}>Replied</option>
            </x-select>

            <x-select name="rating">
                <option value="">All Ratings</option>
                @for($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" {{ (int)($filters['rating'] ?? '') === $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                @endfor
            </x-select>

            <button type="submit" class="btn-primary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>

            @if($filters['status'] || $filters['rating'] || $filters['search'])
                <a href="{{ route('reviews.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>
    </x-card>

    <!-- Reviews List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
            <x-card class="hover:border-slate-600 transition-colors" x-data="reviewItem{{ $review->id }}()">
                <div class="flex gap-4">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($review->reviewer_photo_url)
                            <img src="{{ $review->reviewer_photo_url }}" alt="" class="w-12 h-12 rounded-full">
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-full flex items-center justify-center text-slate-900 font-semibold text-lg">
                                {{ substr($review->reviewer_name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="font-semibold text-white">{{ $review->reviewer_name }}</span>
                            <x-star-rating :rating="$review->rating" />
                            @if($review->status === 'unreplied')
                                <x-badge type="warning">Needs Reply</x-badge>
                            @else
                                <x-badge type="success">Replied</x-badge>
                            @endif
                            <span class="text-slate-500 text-sm">{{ $review->created_at_google?->diffForHumans() }}</span>
                        </div>

                        <p class="text-slate-300 mb-4">{{ $review->comment ?? 'No comment provided' }}</p>

                        <!-- Existing Reply -->
                        @if($review->reply_text)
                            <div class="bg-slate-800/50 rounded-lg p-4 mb-4 border-l-2 border-amber-400">
                                <p class="text-sm text-slate-400 mb-1">Your Reply:</p>
                                <p class="text-slate-300">{{ $review->reply_text }}</p>
                            </div>
                        @endif

                        <!-- Quick AI Action Buttons (always visible) -->
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="text-slate-500 text-xs">AI Reply:</span>
                            <button
                                @click="expanded = true; generateDraft('friendly')"
                                :disabled="loading"
                                class="text-xs px-2 py-1 rounded-full bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors disabled:opacity-50"
                            >
                                😊 Friendly
                            </button>
                            <button
                                @click="expanded = true; generateDraft('professional')"
                                :disabled="loading"
                                class="text-xs px-2 py-1 rounded-full bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 transition-colors disabled:opacity-50"
                            >
                                💼 Professional
                            </button>
                            <button
                                @click="expanded = true; generateDraft('recovery')"
                                :disabled="loading"
                                class="text-xs px-2 py-1 rounded-full bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors disabled:opacity-50"
                            >
                                🔧 Recovery
                            </button>
                        </div>

                        <!-- Reply Panel -->
                        <div class="border-t border-slate-700 pt-4 mt-4" x-show="expanded" x-collapse>
                            <!-- AI Draft Buttons -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="text-slate-400 text-sm">Generate AI Draft:</span>
                                <button
                                    @click="generateDraft('friendly')"
                                    :disabled="loading"
                                    class="text-xs px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 transition-colors disabled:opacity-50"
                                >
                                    😊 Friendly
                                </button>
                                <button
                                    @click="generateDraft('professional')"
                                    :disabled="loading"
                                    class="text-xs px-3 py-1 rounded-full bg-purple-500/10 text-purple-400 hover:bg-purple-500/20 transition-colors disabled:opacity-50"
                                >
                                    💼 Professional
                                </button>
                                <button
                                    @click="generateDraft('recovery')"
                                    :disabled="loading"
                                    class="text-xs px-3 py-1 rounded-full bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 transition-colors disabled:opacity-50"
                                >
                                    🔧 Recovery
                                </button>
                                <button
                                    @click="generateDraft(null)"
                                    :disabled="loading"
                                    class="text-xs px-3 py-1 rounded-full bg-green-500/10 text-green-400 hover:bg-green-500/20 transition-colors disabled:opacity-50"
                                >
                                    ✨ All Three
                                </button>
                            </div>

                            <!-- Loading -->
                            <div x-show="loading" class="flex items-center gap-2 text-amber-400 mb-4">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm">Generating drafts...</span>
                            </div>

                            <!-- Draft Pills -->
                            <div x-show="Object.keys(drafts).length > 0" class="mb-4">
                                <p class="text-slate-400 text-sm mb-2">Click to use draft:</p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="(draft, tone) in drafts" :key="tone">
                                        <button
                                            @click="replyText = draft"
                                            class="text-xs px-3 py-2 rounded-lg bg-slate-700 text-slate-300 hover:bg-slate-600 transition-colors text-left max-w-xs truncate"
                                        >
                                            <span class="font-medium capitalize" x-text="tone + ':'"></span>
                                            <span x-text="draft.substring(0, 50) + '...'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Reply Textarea -->
                            <x-textarea
                                x-model="replyText"
                                placeholder="Write your reply..."
                                rows="3"
                                class="mb-4"
                            />

                            <!-- Submit -->
                            <div class="flex justify-end gap-2">
                                <button @click="expanded = false" class="btn-secondary">Cancel</button>
                                <button
                                    @click="postReply({{ $review->id }})"
                                    :disabled="!replyText || loading"
                                    class="btn-primary disabled:opacity-50"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    Post Reply
                                </button>
                            </div>
                        </div>

                        <!-- Expand Button -->
                        <button
                            @click="expanded = !expanded"
                            class="text-amber-400 text-sm hover:underline mt-2"
                            x-show="!expanded"
                        >
                            {{ $review->status === 'replied' ? 'Update Reply' : 'Write Reply' }} →
                        </button>
                    </div>
                </div>

                <script>
                    function reviewItem{{ $review->id }}() {
                        return {
                            expanded: false,
                            replyText: '',
                            loading: false,
                            drafts: @js($review->ai_drafts ?? []),
                            reviewId: {{ $review->id }},
                            async generateDraft(tone) {
                                this.loading = true;
                                try {
                                    const response = await fetch(`/reviews/{{ $review->id }}/draft`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({ tone })
                                    });
                                    const data = await response.json();

                                    // Poll for completion
                                    await this.pollDraftStatus();
                                } catch (error) {
                                    console.error('Failed to generate draft:', error);
                                } finally {
                                    this.loading = false;
                                }
                            },
                            async pollDraftStatus() {
                                const maxAttempts = 30;
                                let attempts = 0;

                                while (attempts < maxAttempts) {
                                    await new Promise(resolve => setTimeout(resolve, 1000));
                                    const response = await fetch(`/reviews/{{ $review->id }}/draft-status`);
                                    const data = await response.json();

                                    if (data.status === 'completed') {
                                        this.drafts = data.drafts;
                                        return;
                                    } else if (data.status === 'failed') {
                                        alert('Failed to generate drafts. Please try again.');
                                        return;
                                    }
                                    attempts++;
                                }
                            },
                            async postReply(reviewId) {
                                if (!this.replyText) return;

                                this.loading = true;
                                try {
                                    const response = await fetch(`/reviews/${reviewId}/reply`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({ reply: this.replyText })
                                    });
                                    const data = await response.json();

                                    if (data.success) {
                                        window.location.reload();
                                    } else {
                                        alert(data.error || 'Failed to post reply');
                                    }
                                } catch (error) {
                                    console.error('Failed to post reply:', error);
                                    alert('Failed to post reply. Please try again.');
                                } finally {
                                    this.loading = false;
                                }
                            }
                        };
                    }
                </script>
            </x-card>
        @empty
            <x-card class="text-center py-12">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <h3 class="text-lg font-medium text-white mb-2">No Reviews Found</h3>
                <p class="text-slate-400">
                    @if($filters['status'] || $filters['rating'] || $filters['search'])
                        Try adjusting your filters.
                    @else
                        Sync your reviews from Google to get started.
                    @endif
                </p>
            </x-card>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $reviews->links() }}
    </div>
</div>

<script>
function reviewsPage() {
    return {
        async generateDraft(reviewId, tone) {
            // Handled per-review
        }
    };
}
</script>
@endsection
