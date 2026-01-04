@props(['type' => 'default'])

@php
$classes = match($type) {
    'success' => 'bg-green-500/10 text-green-400 border-green-500/20',
    'warning' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
    'error' => 'bg-red-500/10 text-red-400 border-red-500/20',
    'info' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
    default => 'bg-slate-700 text-slate-300 border-slate-600',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ' . $classes]) }}>
    {{ $slot }}
</span>
