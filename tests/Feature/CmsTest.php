<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\User;
use App\Filament\Resources\MenuItems\Pages\ManageMenuItems;
use App\Filament\Resources\PageResource\Pages\CreatePage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CmsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shares_active_menu_items_globally()
    {
        // Create active and inactive menu items
        $activeHeader = MenuItem::create([
            'label' => 'Dynamic Header Link',
            'url' => '/dynamic-header',
            'location' => 'header',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $inactiveHeader = MenuItem::create([
            'label' => 'Inactive Header Link',
            'url' => '/inactive-header',
            'location' => 'header',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $activeFooter = MenuItem::create([
            'label' => 'Dynamic Footer Link',
            'url' => '/dynamic-footer',
            'location' => 'footer',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);

        // Check view shared variables
        $response->assertViewHas('headerMenus', function ($menus) use ($activeHeader, $inactiveHeader) {
            return $menus->contains('id', $activeHeader->id) && !$menus->contains('id', $inactiveHeader->id);
        });

        $response->assertViewHas('footerMenus', function ($menus) use ($activeFooter) {
            return $menus->contains('id', $activeFooter->id);
        });

        // Check page content contains active labels/URLs
        $response->assertSee('Dynamic Header Link');
        $response->assertSee('/dynamic-header');
        $response->assertSee('Dynamic Footer Link');
        $response->assertSee('/dynamic-footer');

        $response->assertDontSee('Inactive Header Link');
    }

    /** @test */
    public function page_content_accessor_supports_both_html_and_blocks_json()
    {
        // Legacy HTML page
        $htmlPage = Page::create([
            'title' => 'HTML Page',
            'slug' => 'html-page',
            'content' => '<h1>Hello World</h1>',
        ]);

        $this->assertEquals('<h1>Hello World</h1>', $htmlPage->content);

        // JSON Page Builder page
        $blocks = [
            [
                'type' => 'paragraph',
                'data' => ['content' => '<p>Builder Content</p>']
            ],
            [
                'type' => 'hero',
                'data' => [
                    'title' => 'Dynamic Block Hero',
                    'subtitle' => 'Subtitle text',
                    'bg_style' => 'teal'
                ]
            ]
        ];

        $jsonPage = Page::create([
            'title' => 'JSON Page',
            'slug' => 'json-page',
            'content' => $blocks,
        ]);

        $this->assertIsArray($jsonPage->content);
        $this->assertEquals('paragraph', $jsonPage->content[0]['type']);
        $this->assertEquals('Dynamic Block Hero', $jsonPage->content[1]['data']['title']);
    }

    /** @test */
    public function it_resolves_custom_page_routes_and_renders_correctly()
    {
        // 1. Test legacy HTML rendering
        $htmlPage = Page::create([
            'title' => 'Custom Info Page',
            'slug' => 'info-page',
            'content' => '<div>This is custom legacy content.</div>',
        ]);

        $response = $this->get('/info-page');
        $response->assertStatus(200);
        $response->assertSee('Custom Info Page');
        $response->assertSee('This is custom legacy content.');

        // 2. Test Block Builder rendering
        $blocks = [
            [
                'type' => 'paragraph',
                'data' => ['content' => '<p>Paragraph block content</p>']
            ],
            [
                'type' => 'hero',
                'data' => [
                    'title' => 'Dynamic Hero Block Title',
                    'subtitle' => 'Dynamic Hero Subtitle',
                    'cta_text' => 'Hero Click',
                    'cta_url' => '/click',
                    'bg_style' => 'teal'
                ]
            ],
            [
                'type' => 'features',
                'data' => [
                    'title' => 'Benefits',
                    'subtitle' => 'Our benefits list',
                    'items' => [
                        [
                            'title' => 'Fast Speed',
                            'description' => 'Fast processing time',
                            'icon' => 'fa-bolt'
                        ]
                    ]
                ]
            ]
        ];

        Page::create([
            'title' => 'Block Page',
            'slug' => 'block-page',
            'content' => $blocks,
        ]);

        $response = $this->get('/block-page');
        $response->assertStatus(200);
        $response->assertSee('Block Page');
        $response->assertSee('Paragraph block content');
        $response->assertSee('Dynamic Hero Block Title');
        $response->assertSee('Dynamic Hero Subtitle');
        $response->assertSee('Hero Click');
        $response->assertSee('Fast Speed');
        $response->assertSee('Fast processing time');
        $response->assertSee('fa-bolt');
    }

    /** @test */
    public function unknown_slugs_return_404()
    {
        $response = $this->get('/non-existent-page-slug');
        $response->assertStatus(404);
    }

    /** @test */
    public function wildcard_route_does_not_override_static_routes()
    {
        // The blog route is static, let's verify it still triggers correctly
        $response = $this->get('/blog');
        $response->assertStatus(200);
        $response->assertViewIs('blog');
    }

    /** @test */
    public function admin_can_create_menu_items_via_filament_form()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageMenuItems::class)
            ->mountAction('create')
            ->setActionData([
                'label' => 'New Dynamic Link',
                'url' => '/dynamic-test',
                'location' => 'header',
                'target' => '_self',
                'sort_order' => 10,
                'is_active' => true,
            ])
            ->callMountedAction()
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('menu_items', [
            'label' => 'New Dynamic Link',
            'url' => '/dynamic-test',
        ]);
    }

    /** @test */
    public function admin_can_create_pages_with_builder_blocks_via_filament_form()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_admin' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'E2E Builder Page',
                'slug' => 'e2e-builder',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'data' => [
                            'content' => '<p>Form paragraph content</p>',
                        ],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('pages', [
            'title' => 'E2E Builder Page',
            'slug' => 'e2e-builder',
        ]);

        $page = Page::where('slug', 'e2e-builder')->first();
        $this->assertIsArray($page->content);
        $this->assertEquals('paragraph', $page->content[0]['type']);
    }
}
