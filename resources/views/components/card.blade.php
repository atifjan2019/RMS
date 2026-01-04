@props(['class' => ''])

<div {{ $attributes->merge(['class' => 'bg-slate-800/50 backdrop-blur-sm rounded-xl border border-slate-700 p-6 ' . $class]) }}>
    {{ $slot }}
</div>
