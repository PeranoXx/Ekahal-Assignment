@props([
    'label' => null,
    'name',
    'id' => null,
    'value' => null,
    'placeholder' => '',
    'height' => 300,
    'required' => false,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
@endphp

<div class="flex flex-col gap-sm" x-data>
    @if($label)
        <label class="font-label-sm text-label-sm text-on-surface" for="{{ $id }}">
            {{ $label }}@if($required)<span class="text-error ml-0.5">*</span>@endif
        </label>
    @endif

    <div class="relative w-full @error($name) border border-error rounded-lg overflow-hidden @enderror">
        <textarea 
            id="{{ $id }}" 
            name="{{ $name }}"
            {{ $attributes->class([
                'w-full bg-surface-container-lowest border rounded px-md py-3 font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant focus:outline-none focus:border-primary transition-all invisible',
                'border-outline-variant' => !$hasError,
                'border-error' => $hasError,
            ]) }}
            placeholder="{{ $placeholder }}"
            style="height: {{ $height }}px;"
        >{{ $value ?? old($name) }}</textarea>
    </div>

    @error($name)
        <p class="font-label-sm text-sm text-error mt-1">{{ $message }}</p>
    @enderror
</div>

@once
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    @endpush
@endonce

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#{{ $id }}',
                height: {{ $height }},
                menubar: false,
                plugins: 'lists link image table code wordcount help',
                toolbar:
                    'undo redo | accordion accordionremove | \
                    importword exportword exportpdf | math | \
                    blocks fontfamily fontsize | bold italic underline strikethrough | \
                    align numlist bullist | link image | table media | \
                    lineheight outdent indent | forecolor backcolor removeformat | \
                    charmap emoticons ',
                branding: false,
                promotion: false,
                skin: 'oxide',
                content_css: 'default',
                content_style: 'body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif; font-size: 14px; color: #171d1d; }',
                setup: function (editor) {
                    editor.on('init change keyup', function () {
                        editor.save();
                    });
                }
            });
        }
    });
</script>
@endpush
