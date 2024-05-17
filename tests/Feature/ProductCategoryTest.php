<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\SedController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_getCategoryProducts_with_cache_enabled_and_no_existing_cache()
    {
        // Mock SedController behavior
        $mockSedController = $this->createMock(SedController::class);
        $mockSedController->method('syncProductsAPI')->willReturn(['data' => '...']);
        $this->app->instance(SedController::class, $mockSedController);

        // Enable cache and clear existing cache
        Config::set('cache.enabled', true);
        Cache::flush();

        $controller = new ProductController();
        $products = $controller->getCategoryProducts('group1', 'categoryA');

        // Assertions
        $this->assertCount(0, $products); // No products yet, needs to be synced
        $this->assertTrue(Cache::has('sync_products_last_run'));
        $this->assertTrue(Cache::has('group'));
        $this->assertEquals('group1', Cache::get('group'));
        $this->assertInstanceOf(Product::class, $products->first()); // Empty collection of Products
        $this->assertEmpty($products->toArray()); // Empty array of product data
        $this->expectMessage('cache group group1'); // Verify Log message for cache put
        Log::shouldHaveLogged('error');
        // Verify Log message for cache clear (using Log facade)
        $logs = Log::channel('error')->get();  // Retrieve logs from the "error" channel
        $this->assertCount(1, $logs);           // Assert there's one log message
        $this->assertContains('cache clear group group1', $logs[0]);  // Assert the message exists
    }


    public function test_getCategoryProducts_with_cache_enabled_and_existing_cache_different_group()
    {
        // Mock SedController behavior (not called here)
        $mockSedController = $this->createMock(SedController::class);
        $this->app->instance(SedController::class, $mockSedController);

        // Enable cache and set existing group
        Config::set('cache.enabled', true);
        Cache::put('group', 'group2');

        $controller = new ProductController();
        $products = $controller->getCategoryProducts('group1', 'categoryA');

        // Assertions
        $this->assertCount(0, $products); // No products yet, needs to be cleared for new group
        $this->assertTrue(Cache::has('group'));
        $this->assertEquals('group1', Cache::get('group'));
        $this->assertInstanceOf(Product::class, $products->first()); // Empty collection of Products
        $this->assertEmpty($products->toArray()); // Empty array of product data

        // Verify Log message for cache clear (using Log facade)
        $logs = Log::channel('error')->get();  // Retrieve logs from the "error" channel
        $this->assertCount(1, $logs);           // Assert there's one log message
        $this->assertContains('cache clear group group1', $logs[0]);  // Assert the message exists


    }



    public function test_getCategoryProducts_with_cache_disabled()
    {
        Config::set('cache.enabled', false);

        // Seed some products (optional)
        Product::factory()->count(5)->create(['department' => 'group1', 'category' => 'categoryA']);

        $controller = new ProductController();
        $products = $controller->getCategoryProducts('group1', 'categoryA');

        // Assertions
        $this->assertCount(5, $products);
        $this->assertFalse(Cache::has('sync_products_last_run'));
        $this->assertFalse(Cache::has('group'));
        $this->assertInstanceOf(Product::class, $products->first()); // Collection of Products
        $this->assertNotEmpty($products->toArray()); // Array of product data
        Log::shouldNotHaveLogged('error'); // No log message expected
    }

    public function test_getCategoryProducts_filters_by_department_and_category()
    {
        // Seed some products (assuming department and category are unique fields)
        Product::factory()->count(10)->create();
        Product::factory()->count(5)->create(['department' => 'group1', 'category' => 'categoryA']);

        $controller = new ProductController();
        $products = $controller->getCategoryProducts('group1', 'categoryA');

        // Assertions
        $this->assertCount(5, $products);
        $this->assertTrue($products->every(function ($product) {
            return $product->department === 'group1' && $product->category === 'categoryA';
        }));
    }
}
