<?php

namespace Tests\Feature;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_render_product_index()
    {
        $this->actingAs($this->admin)->get(ProductResource::getUrl('index'))->assertSuccessful();
    }

    public function test_can_render_product_create()
    {
        $this->actingAs($this->admin)->get(ProductResource::getUrl('create'))->assertSuccessful();
    }

    public function test_can_render_product_edit()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'content_json' => []
        ]);

        $this->actingAs($this->admin)->get(ProductResource::getUrl('edit', ['record' => $product]))->assertSuccessful();
    }
}
