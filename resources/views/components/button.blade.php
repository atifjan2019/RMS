@props(['type' => 'primary'])

@php
$classes = match($type) {
    'secondary' => 'bg-slate-700 text-white hover:bg-slate-600',
    'danger' => 'bg-red-600 text-white hover:bg-red-500',
    default => 'bg-gradient-to-r from-amber-400 to-amber-500 text-slate-900 hover:from-amber-300 hover:to-amber-400',
};
@endphp

<button {{ $attributes->merge(['class' => 'inline-flex items-center justify-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 ' . $classes]) }}>
    {{ $slot }}
</button>
