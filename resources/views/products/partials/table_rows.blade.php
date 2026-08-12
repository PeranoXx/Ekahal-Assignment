@forelse($products as $product)
<tr class="hover:bg-surface-container-low transition-colors group {{ $product->trashed() ? 'opacity-60' : '' }}">
    <!-- Image -->
    <td class="py-md px-lg shrink-0">
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="w-12 h-12 object-cover rounded border border-outline-variant" />
        @else
            <div class="w-12 h-12 rounded border border-outline-variant bg-surface-container-high flex items-center justify-center text-on-surface-variant" title="No Image">
                <span class="material-symbols-outlined text-[20px]">image</span>
            </div>
        @endif
    </td>
    <!-- Title / Slug -->
    <td class="py-md px-lg font-medium text-on-surface">
        <div class="font-title-md text-[16px] leading-tight text-on-surface">
            {{ $product->title }}
        </div>
        <div class="text-[12px] text-on-surface-variant font-mono mt-0.5">
            {{ $product->slug }}
        </div>
    </td>
    <!-- Unit Price -->
    <td class="py-md px-lg text-on-surface font-semibold">
        ₹{{ number_format($product->unit_price, 2) }}
    </td>
    <!-- Date Available -->
    <td class="py-md px-lg text-on-surface-variant">
        {{ $product->date_available ? $product->date_available->format('M d, Y') : 'N/A' }}
    </td>
    <!-- Stock -->
    <td class="py-md px-lg text-on-surface-variant">
        <span class="font-semibold {{ $product->stock === 0 ? 'text-error' : 'text-on-surface' }}">
            {{ $product->stock }}
        </span>
    </td>
    <!-- Status -->
    <td class="py-md px-lg">
        @if ($product->status === 'active')
            <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full bg-[#E0F2E9] text-[#1D7A46] font-label-caps text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#1D7A46]"></span>
                ACTIVE
            </span>
        @else
            <span class="inline-flex items-center gap-xs px-sm py-xs rounded-full bg-surface-container-highest text-on-surface-variant border border-outline-variant font-label-caps text-[10px]">
                <span class="w-1.5 h-1.5 rounded-full bg-on-surface-variant"></span>
                INACTIVE
            </span>
        @endif
    </td>
    <!-- Actions -->
    <td class="py-md px-lg text-right">
        <div class="flex items-center justify-end gap-2 min-h-[24px]">
            @can('product-update')
            <a href="{{ route('products.edit', $product) }}" class="p-xs text-on-surface-variant hover:text-primary transition-colors flex items-center" title="Edit">
                <span class="material-symbols-outlined text-[18px]">edit</span>
            </a>
            @endcan

            @can('product-delete')
            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline m-0 p-0" onsubmit="return confirm('Are you sure you want to delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-xs text-error hover:opacity-85 flex items-center bg-transparent border-0 cursor-pointer" title="Delete">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                </button>
            </form>
            @endcan
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="p-8 text-center text-on-surface-variant font-body-sm">
        No products found.
    </td>
</tr>
@endforelse
