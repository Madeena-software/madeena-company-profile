<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }

    public function test_artikel_index_loads_successfully()
    {
        $this->assertEquals('/artikel', route('artikel.index', [], false));

        $user = \App\Models\User::factory()->create();
        Post::create([
            'title' => 'Test Artikel 1',
            'slug' => 'test-artikel-1',
            'content_json' => [],
            'excerpt' => 'Test Excerpt',
            'user_id' => $user->id,
            'published_at' => now(),
            'is_published' => true,
        ]);

        $response = $this->get(route('artikel.index'));
        $response->assertStatus(200);
        $response->assertSee('Test Artikel 1');
    }

    public function test_product_show_loads_successfully()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'content_json' => [],
            'is_active' => true
        ]);

        $response = $this->get(route('product.show', $product));
        $response->assertStatus(200);
    }

    public function test_post_show_loads_successfully_when_published()
    {
        $user = \App\Models\User::factory()->create();
        $post = Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'content_json' => [],
            'excerpt' => 'Test Excerpt',
            'user_id' => $user->id,
            'published_at' => now(),
            'is_published' => true
        ]);

        $this->assertEquals('/artikel/test-post', route('post.show', $post, false));

        $response = $this->get(route('post.show', $post));
        $response->assertStatus(200);
        $response->assertSee(route('artikel.index'));
        $response->assertSee('Kembali ke Artikel');
    }

    public function test_post_show_returns_404_when_unpublished()
    {
        $user = \App\Models\User::factory()->create();
        $post = Post::create([
            'title' => 'Unpublished Post',
            'slug' => 'unpublished-post',
            'content_json' => [],
            'excerpt' => 'Draft Excerpt',
            'user_id' => $user->id,
            'published_at' => null,
            'is_published' => false,
        ]);

        $response = $this->get(route('post.show', $post));
        $response->assertStatus(404);
    }
}
