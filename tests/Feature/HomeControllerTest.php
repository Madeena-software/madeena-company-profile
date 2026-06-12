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

    public function test_blog_index_loads_successfully()
    {
        $response = $this->get(route('blog.index'));
        $response->assertStatus(200);
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

    public function test_post_show_loads_successfully()
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

        $response = $this->get(route('post.show', $post));
        $response->assertStatus(200);
    }
}
