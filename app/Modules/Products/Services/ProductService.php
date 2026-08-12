<?php

namespace App\Modules\Products\Services;

use App\Modules\Products\Models\Product;
use App\Modules\Products\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected ProductRepository $productRepository;

    /**
     * Create a new service instance.
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Fetch paginated list of products.
     */
    public function getPaginatedProducts(int $perPage = 10, ?string $search = null, ?string $status = 'all', ?string $sortBy = null, ?string $sortOrder = 'asc'): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage, $search, $status, $sortBy, $sortOrder);
    }

    /**
     * Create a new product and handle image upload.
     */
    public function createProduct(array $data): Product
    {
        if (isset($data['image_file']) && $data['image_file']->isValid()) {
            $path = $data['image_file']->store('products', 'public');
            $data['image'] = $path;
        }
        unset($data['image_file']);

        return $this->productRepository->create($data);
    }

    /**
     * Update an existing product and replace image if uploaded.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        if (isset($data['image_file']) && $data['image_file']->isValid()) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $data['image_file']->store('products', 'public');
            $data['image'] = $path;
        }
        unset($data['image_file']);

        $this->productRepository->update($product, $data);

        return $product;
    }

    /**
     * Delete a product and optionally remove image.
     */
    public function deleteProduct(Product $product): bool
    {
        // For SoftDeletes, we might want to keep the image in case of restoration.
        // If we want to fully remove it on hard delete, we would do it in forceDelete.
        return $this->productRepository->delete($product);
    }
}
