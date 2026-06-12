<?php

namespace Tests\Feature\Models;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_casts_content_json_to_array()
    {
        $content = [
            ['type' => 'hero', 'data' => ['title' => 'Test']],
        ];

        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'content_json' => $content,
        ]);

        $this->assertIsArray($product->content_json);
        $this->assertEquals($content, $product->content_json);
    }
}
