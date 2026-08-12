@props([
    'type' => 'submit',
    'variant' => 'primary',
])

@php
    $classes = match($variant) {
        'primary' => 'w-full bg-primary text-on-primary font-label-md text-label-md py-3 rounded hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-sm mt-sm cursor-pointer',
        'secondary' => 'px-4 py-2 text-sm font-semibold rounded-xl border border-zinc-200 text-zinc-700 bg-white hover:bg-zinc-50 hover:text-zinc-900 transition-all duration-200 cursor-pointer',
        default => 'px-4 py-2 font-semibold rounded transition-all cursor-pointer',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
