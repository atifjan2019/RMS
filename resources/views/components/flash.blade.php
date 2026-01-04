@props(['type' => 'success', 'message'])

@php
$classes = match($type) {
    'success' => 'bg-green-500/10 border-green-500/20 text-green-400',
    'error' => 'bg-red-500/10 border-red-500/20 text-red-400',
    'warning' => 'bg-amber-500/10 border-amber-500/20 text-amber-400',
    default => 'bg-blue-500/10 border-blue-500/20 text-blue-400',
};
@endphp

<div x-data="{ show: true }" x-show="show" x-transition class="mb-4 p-4 rounded-lg border {{ $classes }}" role="alert">
    <div class="flex items-center justify-between">
        <span>{{ $message }}</span>
        <button @click="show = false" class="ml-4 hover:opacity-75">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
