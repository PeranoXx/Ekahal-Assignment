@extends('layouts.dashboard')

@section('title', $product->title)

@section('content')
<!-- Canvas -->
<main class="flex-1 p-margin-desktop max-w-7xl mx-auto w-full space-y-lg">
    <!-- Breadcrumbs -->
    <div class="flex items-center gap-xs font-label text-label-caps text-on-surface-variant">
        <a href="{{ route('products.index') }}" class="hover:text-primary transition-colors">Products</a>
        <span class="material-symbols-outlined text-[16px] text-outline-variant">chevron_right</span>
        <span class="text-on-surface">{{ $product->title }}</span>
    </div>

    <!-- Page Header & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-md pb-md border-b border-outline-variant">
        <div>
            <div class="flex items-center gap-sm mb-xs">
                <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $product->title }}</h2>
                
                <!-- Stock Badge -->
                @if($product->stock == 0)
                    <span class="inline-flex items-center gap-xs px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-label-caps text-[10px] font-bold">
                        <span class="material-symbols-outlined text-[12px] font-bold">cancel</span>
                        OUT OF STOCK
                    </span>
                @else
                    <span class="inline-flex items-center gap-xs px-2.5 py-0.5 rounded-full bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD] font-label-caps text-[10px] font-bold">
                        <span class="material-symbols-outlined text-[12px] font-bold">check_circle</span>
                        IN STOCK
                    </span>
                @endif
            </div>
            <p class="font-mono text-body-sm text-on-surface-variant">Slug: {{ $product->slug }}</p>
        </div>
        
        <div class="flex gap-sm w-full md:w-auto">
            <!-- Edit Product -->
            @can('product-update')
            <a href="{{ route('products.edit', $product) }}" class="flex items-center justify-center gap-2 px-4 py-2 bg-primary text-on-primary rounded font-label-md text-label-md hover:bg-surface-tint transition-all shadow-sm w-full md:w-auto">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                Edit Product
            </a>
            @endcan
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-md items-start">
        <!-- Left Side Column (Image & Description) -->
        <div class="lg:col-span-2 space-y-md">
            <!-- Product Image -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-md flex items-center justify-center h-96 relative overflow-hidden shadow-sm">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="max-w-full max-h-full object-contain rounded-lg" />
                @else
                    <div class="flex flex-col items-center justify-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-[64px] mb-2 text-outline-variant">image</span>
                        <p class="font-body-sm">No image available</p>
                    </div>
                @endif
            </div>

            <!-- Product Description -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
                <h3 class="font-title-md text-title-md text-on-surface font-bold mb-md pb-xs border-b border-outline-variant/30">Product Description</h3>
                <div class="tinymce-content text-on-surface leading-relaxed">
                    {!! $product->description !!}
                </div>
            </div>
        </div>

        <!-- Right Side Column (Summary Panels) -->
        <div class="lg:col-span-1 space-y-md">
            <!-- Inventory Summary Card -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm space-y-md">
                <div class="flex items-center gap-xs text-on-surface font-bold">
                    <span class="material-symbols-outlined text-[22px] text-primary">inventory_2</span>
                    <h3 class="font-title-md text-title-md">Inventory Summary</h3>
                </div>
                
                <div class="">
                    <!-- Current Stock -->
                    <div class="bg-surface-container-low border border-outline-variant/50 rounded-xl p-md">
                        <span class="block text-[11px] font-bold text-on-surface-variant uppercase tracking-wider mb-xs">Current Stock</span>
                        <span class="text-headline-lg font-bold text-on-surface leading-none block">
                            {{ number_format($product->stock) }}
                        </span>
                    </div>
                </div>

            </div>

            <!-- Pricing Card -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm space-y-sm">
                <div class="flex items-center gap-xs text-on-surface font-bold">
                    <span class="material-symbols-outlined text-[20px] text-primary">payments</span>
                    <h3 class="font-title-md text-title-md">Pricing</h3>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-3xl text-on-surface font-bold">₹{{ number_format($product->unit_price, 2) }}</span>
                    <span class="text-on-surface-variant font-label text-[12px] uppercase">/ unit</span>
                </div>
            </div>

            <!-- Specifications Card -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm space-y-md">
                <div class="flex items-center gap-xs text-on-surface font-bold">
                    <span class="material-symbols-outlined text-[22px] text-primary">list_alt</span>
                    <h3 class="font-title-md text-title-md">Specifications</h3>
                </div>
                
                <div class="flex flex-col divide-y divide-outline-variant/30 text-body-sm">
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-on-surface-variant font-medium">Status</span>
                        @if ($product->status === 'active')
                            <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full bg-[#E0F2E9] text-[#1D7A46] font-label-caps text-[10px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#1D7A46]"></span>
                                ACTIVE
                            </span>
                        @else
                            <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full bg-surface-container-highest text-on-surface-variant border border-outline-variant font-label-caps text-[10px] font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-on-surface-variant"></span>
                                INACTIVE
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-on-surface-variant font-medium">Available From</span>
                        <span class="font-medium text-on-surface">{{ $product->date_available ? $product->date_available->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-on-surface-variant font-medium">Created At</span>
                        <span class="font-medium text-on-surface">{{ $product->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2.5">
                        <span class="text-on-surface-variant font-medium">Updated At</span>
                        <span class="font-medium text-on-surface">{{ $product->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
