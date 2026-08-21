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

    public function test_existing_plain_hero_and_cta_descriptions_render_properly()
    {
        \App\Models\Setting::setJson('homepage_sections', [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'Hero Plain Title',
                            'description' => 'PT Madeena menyediakan layanan radiologi terlengkap.',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'CTA Title',
                    'subtitle' => 'Dukung kemandirian alkes buatan Indonesia.',
                    'button_text' => 'Kontak',
                    'button_url' => '#kontak',
                ],
            ],
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('PT Madeena menyediakan layanan radiologi terlengkap.');
        $response->assertSee('Dukung kemandirian alkes buatan Indonesia.');
    }

    public function test_rich_hero_and_cta_descriptions_render_paragraphs_formatting_and_links()
    {
        \App\Models\Setting::setJson('homepage_sections', [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'Rich Hero Title',
                            'description' => '<p>Paragraph 1 with <strong>bold text</strong>.</p><p>Paragraph 2 with <a href="https://madeena.co.id">official link</a>.</p>',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'Rich CTA Title',
                    'subtitle' => '<p>First paragraph.</p><ul><li>Benefit 1</li><li>Benefit 2</li></ul>',
                    'button_text' => 'Hubungi Kami',
                    'button_url' => 'https://madeena.co.id/kontak',
                ],
            ],
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Paragraph 1 with <strong>bold text</strong>.', false);
        $response->assertSee('<a href="https://madeena.co.id">official link</a>', false);
        $response->assertSee('First paragraph.');
        $response->assertSee('<li>Benefit 1</li>', false);
        $response->assertSee('<li>Benefit 2</li>', false);
    }

    public function test_rich_hero_and_cta_descriptions_sanitize_script_injection()
    {
        \App\Models\Setting::setJson('homepage_sections', [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'XSS Test Hero',
                            'description' => '<p>Safe Paragraph <script>alert("hero-xss")</script><a href="javascript:alert(1)">Click</a></p>',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'XSS Test CTA',
                    'subtitle' => '<p>Safe CTA <script>alert("cta-xss")</script></p>',
                    'button_text' => 'Click',
                    'button_url' => '#',
                ],
            ],
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertDontSee('<script>alert("hero-xss")</script>', false);
        $response->assertDontSee('<script>alert("cta-xss")</script>', false);
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertSee('Safe Paragraph');
        $response->assertSee('Safe CTA');
    }

    public function test_hero_template_uses_expanded_copy_width_and_allows_title_wrapping()
    {
        \App\Models\Setting::setJson('homepage_sections', [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'A Very Long Corporate Heading for PT Madeena Karya Indonesia Radiography Innovation',
                            'description' => 'Detailed hero description.',
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Hero copy should receive 60-65% width (lg:col-span-7 or lg:col-span-8 in a 12-col grid)
        $response->assertSee('lg:col-span-7', false);
        $response->assertSee('lg:col-span-5', false);

        // Hero title must not have whitespace-nowrap that breaks long titles
        $response->assertDontSee('whitespace-nowrap', false);
        $response->assertSee('break-words', false);
    }
}
