<?php

namespace Tests\Feature;

use App\Modules\Users\Models\User;
use App\Modules\Products\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    /**
     * Test admin can view the products list page.
     */
    public function test_admin_can_view_products_list(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        // Clear default seeded products so empty state assertion works
        Product::query()->forceDelete();

        $response = $this->actingAs($admin)->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Products Management');
        $response->assertSee('No products found.');
    }

    /**
     * Test standard user cannot view products list page.
     */
    public function test_standard_user_cannot_view_products_list(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(403);
    }

    /**
     * Test admin can create a product.
     */
    public function test_admin_can_create_product(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        $response = $this->actingAs($admin)->post('/products', [
            'title' => 'Test Product Desk',
            'slug' => 'test-product-desk',
            'description' => '<p>This is a <strong>cool</strong> product desk.</p>',
            'unit_price' => 129.99,
            'date_available' => '2026-08-15',
            'stock' => 10,
            'status' => 'active',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'title' => 'Test Product Desk',
            'slug' => 'test-product-desk',
            'unit_price' => 129.99,
            'stock' => 10,
            'status' => 'active',
        ]);
    }

    /**
     * Test standard user cannot create a product.
     */
    public function test_standard_user_cannot_create_product(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        $response = $this->actingAs($user)->post('/products', [
            'title' => 'Unauthorized Product',
            'slug' => 'unauthorized-product',
            'description' => '<p>Standard users cannot create.</p>',
            'unit_price' => 99.99,
            'date_available' => '2026-08-15',
            'stock' => 5,
            'status' => 'active',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', ['slug' => 'unauthorized-product']);
    }

    /**
     * Test admin can update a product.
     */
    public function test_admin_can_update_product(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $product = Product::factory()->create([
            'title' => 'Old Product Title',
            'slug' => 'old-product-title',
        ]);

        $response = $this->actingAs($admin)->put("/products/{$product->id}", [
            'title' => 'New Product Title',
            'slug' => 'new-product-title',
            'description' => '<p>Updated description</p>',
            'unit_price' => 149.99,
            'date_available' => '2026-08-20',
            'stock' => 5,
            'status' => 'inactive',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'New Product Title',
            'slug' => 'new-product-title',
            'description' => '<p>Updated description</p>',
            'unit_price' => 149.99,
            'stock' => 5,
            'status' => 'inactive',
        ]);
    }

    /**
     * Test standard user cannot update a product.
     */
    public function test_standard_user_cannot_update_product(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        
        $product = Product::factory()->create([
            'title' => 'Old Product Title',
            'slug' => 'old-product-title',
        ]);

        $response = $this->actingAs($user)->put("/products/{$product->id}", [
            'title' => 'New Product Title',
            'slug' => 'new-product-title',
            'description' => '<p>Updated description</p>',
            'unit_price' => 149.99,
            'date_available' => '2026-08-20',
            'stock' => 5,
            'status' => 'inactive',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Old Product Title',
            'slug' => 'old-product-title',
        ]);
    }

    /**
     * Test admin can delete a product.
     */
    public function test_admin_can_delete_product(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete("/products/{$product->id}");

        $response->assertRedirect('/products');
        $this->assertSoftDeleted($product);
    }

    /**
     * Test standard user cannot delete a product.
     */
    public function test_standard_user_cannot_delete_product(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->delete("/products/{$product->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test admin can search products.
     */
    public function test_admin_can_search_products(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        Product::query()->forceDelete();

        Product::factory()->create(['title' => 'Matching Searchable Chair', 'slug' => 'matching-chair']);
        Product::factory()->create(['title' => 'Not Related Item', 'slug' => 'unrelated-item']);

        $response = $this->actingAs($admin)
            ->get('/products?search=Matching Searchable', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'pagination', 'total']);
        
        $data = $response->json();
        $this->assertStringContainsString('Matching Searchable Chair', $data['html']);
        $this->assertStringNotContainsString('Not Related Item', $data['html']);
        $this->assertEquals(1, $data['total']);
    }

    /**
     * Test admin can filter products by status.
     */
    public function test_admin_can_filter_products_by_status(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        Product::query()->forceDelete();

        Product::factory()->create(['title' => 'Active Product One', 'status' => 'active']);
        Product::factory()->create(['title' => 'Inactive Product Two', 'status' => 'inactive']);

        // Filter active
        $response = $this->actingAs($admin)
            ->get('/products?status=active', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertStringContainsString('Active Product One', $data['html']);
        $this->assertStringNotContainsString('Inactive Product Two', $data['html']);

        // Filter inactive
        $response = $this->actingAs($admin)
            ->get('/products?status=inactive', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertStringNotContainsString('Active Product One', $data['html']);
        $this->assertStringContainsString('Inactive Product Two', $data['html']);
    }

    /**
     * Test admin can sort products by field.
     */
    public function test_admin_can_sort_products(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        Product::query()->forceDelete();

        Product::factory()->create(['title' => 'Apple Phone']);
        Product::factory()->create(['title' => 'Zebra Rug']);

        // Sort by title asc
        $response = $this->actingAs($admin)
            ->get('/products?sort_by=title&sort_order=asc', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        
        $applePos = strpos($data['html'], 'Apple Phone');
        $zebraPos = strpos($data['html'], 'Zebra Rug');
        $this->assertTrue($applePos < $zebraPos);

        // Sort by title desc
        $response = $this->actingAs($admin)
            ->get('/products?sort_by=title&sort_order=desc', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        
        $applePos = strpos($data['html'], 'Apple Phone');
        $zebraPos = strpos($data['html'], 'Zebra Rug');
        $this->assertTrue($zebraPos < $applePos);
    }

    /**
     * Test admin can paginate products.
     */
    public function test_admin_can_paginate_products(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        Product::query()->forceDelete();

        // Create 15 products (default pagination per page is 10)
        Product::factory()->count(15)->create();

        $response = $this->actingAs($admin)
            ->get('/products?page=2', [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertEquals(15, $data['total']);
        $this->assertStringContainsString('Showing', $data['pagination']);
    }

    /**
     * Test admin can view single product details.
     */
    public function test_admin_can_view_single_product_details(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        
        $product = Product::factory()->create([
            'title' => 'Test Detail Product',
            'slug' => 'test-detail-product',
            'description' => '<p>Some <strong>rich</strong> text description</p>',
            'unit_price' => 19.99,
            'stock' => 15,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)
            ->get("/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertViewIs('products.show');
        $response->assertSee('Test Detail Product');
        $response->assertSee('test-detail-product');
        $response->assertSee('<p>Some <strong>rich</strong> text description</p>', false);
    }

    /**
     * Test standard user cannot view single product details.
     */
    public function test_standard_user_cannot_view_single_product_details(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
            ->get("/products/{$product->id}");

        $response->assertStatus(403);
    }
}
