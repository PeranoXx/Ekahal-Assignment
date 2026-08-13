@extends('layouts.dashboard')

@section('title', 'Edit Product')

@section('content')
<!-- Canvas -->
<main class="flex-1 p-margin-desktop max-w-6xl mx-auto w-full">
    <!-- Page Header -->
    <div class="mb-xl">
        <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight mb-xs">Edit Product Details</h2>
        <p class="font-body-md text-body-md text-on-surface-variant">Update the product specifications, pricing, stock levels, or change its image.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-surface-container-lowest border border-outline-variant rounded-lg p-lg shadow-sm">
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-lg">
            @csrf
            @method('PUT')

            <!-- Image Upload Area -->
            <div onclick="document.getElementById('image_file').click()" class="border-2 border-dashed border-outline-variant rounded-xl p-xl flex flex-col items-center justify-center bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group relative">
                <input type="file" id="image_file" name="image_file" accept="image/*" class="absolute opacity-0 w-0 h-0 pointer-events-none" />
                
                <!-- Upload Placeholder State (hidden if current image exists) -->
                <div id="upload-placeholder" class="flex flex-col items-center justify-center @if($product->image) hidden @endif">
                    <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-md group-hover:bg-primary group-hover:text-on-primary transition-colors">
                        <span class="material-symbols-outlined text-primary text-[32px] group-hover:text-on-primary transition-colors">cloud_upload</span>
                    </div>
                    <p class="font-title-md text-body-md text-on-surface font-medium mb-xs">Upload product image</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant text-center">Click to browse. PNG, JPG up to 5MB.</p>
                </div>

                <!-- Current Image State -->
                @if($product->image)
                <div id="current-image-container" class="flex flex-col items-center justify-center">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="w-32 h-32 object-cover rounded-lg border border-outline-variant" />
                    <p class="text-xs text-on-surface-variant mt-2 font-medium">Current Image (Click to replace)</p>
                </div>
                @endif

                <!-- Preview State (initially hidden) -->
                <div id="image-preview-container" class="hidden flex flex-col items-center justify-center">
                    <img id="image-preview" src="#" alt="Preview" class="w-32 h-32 object-cover rounded-lg border border-outline-variant" />
                    <p class="text-xs text-on-surface-variant mt-2 font-mono" id="image-filename"></p>
                </div>
            </div>
            @error('image_file')
                <p class="font-label-sm text-sm text-error mt-1">{{ $message }}</p>
            @enderror

            <!-- Basic Details -->
            <div class="grid grid-cols-1 gap-gutter">
                <!-- Product Name -->
                <x-input 
                    label="Product name" 
                    name="title" 
                    id="title"
                    type="text" 
                    placeholder="e.g. Ergonomic Office Chair" 
                    value="{{ old('title', $product->title) }}" 
                    autofocus
                    required
                />

                <!-- Slug -->
                <x-input 
                    label="Slug" 
                    name="slug" 
                    id="slug"
                    type="text" 
                    placeholder="ergonomic-office-chair" 
                    value="{{ old('slug', $product->slug) }}" 
                    required
                />

                <!-- Description -->
                <x-tinymce 
                    label="Description" 
                    name="description" 
                    id="description"
                    placeholder="Describe the product features, specifications, and materials..."
                    :value="old('description', $product->description)"
                    required
                />
            </div>

            <hr class="border-outline-variant"/>

            <!-- Pricing & Inventory -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                <!-- Unit Price -->
                <x-input 
                    label="Unit Price" 
                    name="unit_price" 
                    type="number" 
                    step="0.01" 
                    prefix="₹"
                    placeholder="0.00" 
                    value="{{ old('unit_price', $product->unit_price) }}"
                    required
                />

                <!-- Stock -->
                <x-input 
                    label="Stock" 
                    name="stock" 
                    type="number"
                    placeholder="0" 
                    value="{{ old('stock', $product->stock) }}"
                    required
                />

                <!-- Date Available -->
                <x-input 
                    label="Date Available" 
                    name="date_available" 
                    type="date" 
                    value="{{ old('date_available', $product->date_available ? $product->date_available->format('Y-m-d') : '') }}"
                    required
                />
            </div>

            <!-- Status Toggle -->
            <div class="flex items-center justify-between p-md border border-outline-variant rounded-lg bg-surface">
                <div>
                    <p class="font-title-md text-body-md text-on-surface font-medium">Active Status</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Product will be visible in the inventory system immediately.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="status" value="inactive" />
                    <input 
                        id="status-toggle" 
                        name="status" 
                        value="active" 
                        class="absolute opacity-0 w-0 h-0 pointer-events-none peer" 
                        type="checkbox" 
                        {{ old('status', $product->status) === 'active' ? 'checked' : '' }} 
                    />
                    <div class="relative w-11 h-6 bg-outline-variant rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-md mt-lg">
                <x-button variant="primary">
                    Update Product
                </x-button>
                <a href="{{ route('products.index') }}" class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Title to Slug Auto-generation
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');
    let manualSlug = true;

    titleInput.addEventListener('input', function() {
        if (!manualSlug || slugInput.value === '') {
            slugInput.value = titleInput.value
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)+/g, '');
        }
    });

    slugInput.addEventListener('input', function() {
        manualSlug = true;
    });

    // Image upload live preview
    const fileInput = document.getElementById('image_file');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const currentImageContainer = document.getElementById('current-image-container');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImg = document.getElementById('image-preview');
    const filenameText = document.getElementById('image-filename');

    fileInput.addEventListener('change', function() {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                filenameText.textContent = file.name;
                
                previewContainer.classList.remove('hidden');
                uploadPlaceholder.classList.add('hidden');
                if (currentImageContainer) {
                    currentImageContainer.classList.add('hidden');
                }
            }
            
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
