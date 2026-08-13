@props([
    'label' => null,
    'name',
    'type' => 'text',
    'id' => null,
    'value' => null,
    'help' => null,
    'prefix' => null,
    'required' => false,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<div class="flex flex-col gap-sm">
    @if($label)
        <label class="font-label-sm text-label-sm text-on-surface" for="{{ $id }}">
            {{ $label }}@if($required)<span class="text-error ml-0.5">*</span>@endif
        </label>
    @endif

    <div class="relative w-full">
        @if($prefix)
            <span class="absolute left-md top-1/2 -translate-y-1/2 text-on-surface-variant/60 font-body-sm">
                {{ $prefix }}
            </span>
        @endif

        <input 
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value ?? old($name) }}"
            {{ $attributes->class([
                'w-full bg-surface-container-lowest border rounded px-md py-3 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:border-primary transition-all',
                'border-outline-variant' => !$hasError,
                'border-error' => $hasError,
                'pl-10' => !empty($prefix),
            ]) }}
        />
    </div>

    @error($name)
        <p class="font-label-sm text-sm text-error">{{ $message }}</p>
    @else
        @if($help)
            <p class="font-label-sm text-sm text-on-surface-variant">{{ $help }}</p>
        @endif
    @enderror
</div>
