<?php

namespace Tests\Feature;

use App\Filament\Pages\HomepageEditor;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Livewire\Livewire;
use Tests\TestCase;

class HomepageEditorTest extends TestCase
{
    use DatabaseTruncation;

    private function sampleHeroSection(string $title): array
    {
        return [
            [
                'type' => 'hero',
                'data' => [
                    'show_in_nav' => true,
                    'nav_label' => 'Beranda',
                    'banners' => [
                        [
                            'title' => $title,
                            'subtitle' => 'Subtitle for ' . $title,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function test_homepage_editor_can_render_for_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(HomepageEditor::getUrl())->assertSuccessful();
    }

    public function test_homepage_editor_forbidden_for_non_admin()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)->get(HomepageEditor::getUrl())->assertForbidden();
    }

    public function test_homepage_editor_redirects_for_guest()
    {
        $this->get(HomepageEditor::getUrl())->assertRedirect();
    }

    public function test_save_writes_draft_and_does_not_publish()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $publishedData = $this->sampleHeroSection('Published Live Title');
        $draftData = $this->sampleHeroSection('Unpublished Draft Title');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', null);

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->fillForm(['sections' => $draftData])
            ->call('save')
            ->assertHasNoFormErrors();

        $savedDraft = Setting::getJson('homepage_sections_draft');
        $this->assertIsArray($savedDraft);
        $this->assertEquals('Unpublished Draft Title', $savedDraft[0]['data']['banners'][0]['title']);

        $published = Setting::getJson('homepage_sections');
        $this->assertIsArray($published);
        $this->assertEquals('Published Live Title', $published[0]['data']['banners'][0]['title']);
    }

    public function test_publish_promotes_draft_to_published_homepage_sections()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $publishedData = $this->sampleHeroSection('Old Published Title');
        $draftData = $this->sampleHeroSection('New Promoted Title');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('publish');

        $published = Setting::getJson('homepage_sections');
        $this->assertIsArray($published);
        $this->assertEquals('New Promoted Title', $published[0]['data']['banners'][0]['title']);
    }

    public function test_publish_action_promotes_draft_via_header_action()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $publishedData = $this->sampleHeroSection('Initial Published Title');
        $draftData = $this->sampleHeroSection('Header Action Promoted Title');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->callAction('publish_to_prod');

        $published = Setting::getJson('homepage_sections');
        $this->assertIsArray($published);
        $this->assertEquals('Header Action Promoted Title', $published[0]['data']['banners'][0]['title']);
    }

    public function test_public_homepage_uses_published_data_not_draft()
    {
        $publishedData = $this->sampleHeroSection('Public Visible Hero');
        $draftData = $this->sampleHeroSection('Secret Draft Hero');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        $response = $this->get('/');

        $response->assertSuccessful();
        $response->assertSee('Public Visible Hero');
        $response->assertDontSee('Secret Draft Hero');
    }

    public function test_admin_preview_uses_draft_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $publishedData = $this->sampleHeroSection('Public Visible Hero');
        $draftData = $this->sampleHeroSection('Secret Draft Hero');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        $response = $this->actingAs($admin)->get('/?preview=true');

        $response->assertSuccessful();
        $response->assertSee('Secret Draft Hero');
        $response->assertDontSee('Public Visible Hero');
    }

    public function test_unauthenticated_preview_does_not_expose_draft_data()
    {
        $publishedData = $this->sampleHeroSection('Public Visible Hero');
        $draftData = $this->sampleHeroSection('Secret Draft Hero');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        $response = $this->get('/?preview=true');

        $response->assertSuccessful();
        $response->assertSee('Public Visible Hero');
        $response->assertDontSee('Secret Draft Hero');
    }

    public function test_non_admin_authenticated_preview_does_not_expose_draft_data()
    {
        $user = User::factory()->create(['role' => 'user']);

        $publishedData = $this->sampleHeroSection('Public Visible Hero');
        $draftData = $this->sampleHeroSection('Secret Draft Hero');

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        $response = $this->actingAs($user)->get('/?preview=true');

        $response->assertSuccessful();
        $response->assertSee('Public Visible Hero');
        $response->assertDontSee('Secret Draft Hero');
    }

    public function test_publish_falls_back_safely_when_no_draft_exists()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $publishedData = $this->sampleHeroSection('Existing Published Title');
        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', null);

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('publish');

        $published = Setting::getJson('homepage_sections');
        $this->assertIsArray($published);
        $this->assertEquals('Existing Published Title', $published[0]['data']['banners'][0]['title']);
    }

    public function test_rich_hero_and_cta_formatting_survives_save_and_publish_cycle()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $richSections = [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'Rich Hero Title',
                            'description' => '<p>Paragraf 1 dengan <strong>tebal</strong>.</p><p>Paragraf 2 <a href="https://example.com">link</a>.</p>',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'Rich CTA Title',
                    'subtitle' => '<ul><li>Fitur 1</li><li>Fitur 2</li></ul>',
                    'button_text' => 'Hubungi',
                    'button_url' => '#kontak',
                ],
            ],
        ];

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->fillForm(['sections' => $richSections])
            ->call('save')
            ->assertHasNoFormErrors();

        $draft = Setting::getJson('homepage_sections_draft');
        $this->assertEquals('<p>Paragraf 1 dengan <strong>tebal</strong>.</p><p>Paragraf 2 <a href="https://example.com">link</a>.</p>', $draft[0]['data']['banners'][0]['description']);
        $this->assertStringContainsString('Fitur 1', $draft[1]['data']['subtitle']);
        $this->assertStringContainsString('Fitur 2', $draft[1]['data']['subtitle']);
        $this->assertStringContainsString('<ul>', $draft[1]['data']['subtitle']);

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('publish');

        $published = Setting::getJson('homepage_sections');
        $this->assertEquals('<p>Paragraf 1 dengan <strong>tebal</strong>.</p><p>Paragraf 2 <a href="https://example.com">link</a>.</p>', $published[0]['data']['banners'][0]['description']);
        $this->assertStringContainsString('Fitur 1', $published[1]['data']['subtitle']);
        $this->assertStringContainsString('Fitur 2', $published[1]['data']['subtitle']);
        $this->assertStringContainsString('<ul>', $published[1]['data']['subtitle']);
    }

    public function test_draft_rich_content_is_isolated_from_public_and_anonymous_preview()
    {
        $publishedData = [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'Live Public Hero',
                            'description' => '<p>Published live copy.</p>',
                        ],
                    ],
                ],
            ],
        ];

        $draftData = [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'Secret Draft Hero',
                            'description' => '<p>Confidential draft copy with <strong>secret info</strong>.</p>',
                        ],
                    ],
                ],
            ],
        ];

        Setting::setJson('homepage_sections', $publishedData);
        Setting::setJson('homepage_sections_draft', $draftData);

        // Anonymous public visitor
        $pubResponse = $this->get('/');
        $pubResponse->assertSuccessful();
        $pubResponse->assertSee('Published live copy.');
        $pubResponse->assertDontSee('Confidential draft copy');

        // Anonymous ?preview=true
        $previewResponse = $this->get('/?preview=true');
        $previewResponse->assertSuccessful();
        $previewResponse->assertSee('Published live copy.');
        $previewResponse->assertDontSee('Confidential draft copy');
    }

    public function test_editor_loads_indonesian_and_english_content_independently()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('ID Published Hero'));
        Setting::setJson('homepage_sections_draft', $this->sampleHeroSection('ID Draft Hero'));
        Setting::setJson('homepage_sections_en', $this->sampleHeroSection('EN Published Hero'));
        Setting::setJson('homepage_sections_en_draft', $this->sampleHeroSection('EN Draft Hero'));

        // Mounts with ID default
        $component = Livewire::actingAs($admin)
            ->test(HomepageEditor::class);

        $this->assertEquals('id', $component->get('activeLocale'));
        $data = $component->get('data');
        $this->assertEquals('ID Draft Hero', array_values(array_values($data['sections'])[0]['data']['banners'])[0]['title']);

        // Switch to English
        $component->call('switchLanguage', 'en');
        $this->assertEquals('en', $component->get('activeLocale'));
        $dataEn = $component->get('data');
        $this->assertEquals('EN Draft Hero', array_values(array_values($dataEn['sections'])[0]['data']['banners'])[0]['title']);
    }

    public function test_editor_english_does_not_fall_back_to_indonesian_content()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('ID Published Hero'));
        Setting::setJson('homepage_sections_draft', null);
        Setting::setJson('homepage_sections_en', null);
        Setting::setJson('homepage_sections_en_draft', null);

        $component = Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'en');

        $data = $component->get('data');
        $this->assertEmpty($data['sections']);
    }

    public function test_save_indonesian_isolates_from_english_settings()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('ID Published'));
        Setting::setJson('homepage_sections_draft', null);
        Setting::setJson('homepage_sections_en', $this->sampleHeroSection('EN Published'));
        Setting::setJson('homepage_sections_en_draft', $this->sampleHeroSection('EN Draft'));

        $newIdDraft = $this->sampleHeroSection('ID New Draft');

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'id')
            ->fillForm(['sections' => $newIdDraft])
            ->call('save')
            ->assertHasNoFormErrors();

        // Check ID draft updated
        $savedIdDraft = Setting::getJson('homepage_sections_draft');
        $this->assertEquals('ID New Draft', $savedIdDraft[0]['data']['banners'][0]['title']);
        $this->assertEquals('ID Published', Setting::getJson('homepage_sections')[0]['data']['banners'][0]['title']);

        // Check EN untouched
        $this->assertEquals('EN Published', Setting::getJson('homepage_sections_en')[0]['data']['banners'][0]['title']);
        $this->assertEquals('EN Draft', Setting::getJson('homepage_sections_en_draft')[0]['data']['banners'][0]['title']);
    }

    public function test_save_english_isolates_from_indonesian_settings()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('ID Published'));
        Setting::setJson('homepage_sections_draft', $this->sampleHeroSection('ID Draft'));
        Setting::setJson('homepage_sections_en', $this->sampleHeroSection('EN Published'));
        Setting::setJson('homepage_sections_en_draft', null);

        $newEnDraft = $this->sampleHeroSection('EN New Draft');

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'en')
            ->fillForm(['sections' => $newEnDraft])
            ->call('save')
            ->assertHasNoFormErrors();

        // Check EN draft updated
        $savedEnDraft = Setting::getJson('homepage_sections_en_draft');
        $this->assertEquals('EN New Draft', $savedEnDraft[0]['data']['banners'][0]['title']);
        $this->assertEquals('EN Published', Setting::getJson('homepage_sections_en')[0]['data']['banners'][0]['title']);

        // Check ID untouched
        $this->assertEquals('ID Published', Setting::getJson('homepage_sections')[0]['data']['banners'][0]['title']);
        $this->assertEquals('ID Draft', Setting::getJson('homepage_sections_draft')[0]['data']['banners'][0]['title']);
    }

    public function test_publish_indonesian_does_not_modify_english_settings()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('Old ID Published'));
        Setting::setJson('homepage_sections_draft', $this->sampleHeroSection('New ID Draft'));
        Setting::setJson('homepage_sections_en', $this->sampleHeroSection('EN Published'));
        Setting::setJson('homepage_sections_en_draft', $this->sampleHeroSection('EN Draft'));

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'id')
            ->call('publish');

        // Check ID published updated
        $this->assertEquals('New ID Draft', Setting::getJson('homepage_sections')[0]['data']['banners'][0]['title']);

        // Check EN untouched
        $this->assertEquals('EN Published', Setting::getJson('homepage_sections_en')[0]['data']['banners'][0]['title']);
        $this->assertEquals('EN Draft', Setting::getJson('homepage_sections_en_draft')[0]['data']['banners'][0]['title']);
    }

    public function test_publish_english_does_not_modify_indonesian_settings()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('ID Published'));
        Setting::setJson('homepage_sections_draft', $this->sampleHeroSection('ID Draft'));
        Setting::setJson('homepage_sections_en', $this->sampleHeroSection('Old EN Published'));
        Setting::setJson('homepage_sections_en_draft', $this->sampleHeroSection('New EN Draft'));

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'en')
            ->call('publish');

        // Check EN published updated
        $this->assertEquals('New EN Draft', Setting::getJson('homepage_sections_en')[0]['data']['banners'][0]['title']);

        // Check ID untouched
        $this->assertEquals('ID Published', Setting::getJson('homepage_sections')[0]['data']['banners'][0]['title']);
        $this->assertEquals('ID Draft', Setting::getJson('homepage_sections_draft')[0]['data']['banners'][0]['title']);
    }

    public function test_navigation_isolation_between_languages()
    {
        $idSection = [
            [
                'type' => 'hero',
                'data' => [
                    'show_in_nav' => true,
                    'nav_label' => 'Beranda ID',
                    'section_id' => 'beranda',
                ],
            ],
        ];

        $enSection = [
            [
                'type' => 'hero',
                'data' => [
                    'show_in_nav' => true,
                    'nav_label' => 'Home EN',
                    'section_id' => 'home',
                ],
            ],
        ];

        Setting::setJson('homepage_sections', $idSection);
        Setting::setJson('homepage_sections_en', $enSection);

        $idNav = HomepageEditor::getNavigation(false, 'id');
        $enNav = HomepageEditor::getNavigation(false, 'en');

        $this->assertCount(1, $idNav);
        $this->assertEquals('Beranda ID', $idNav[0]['label']);
        $this->assertEquals('#beranda', $idNav[0]['anchor']);

        $this->assertCount(1, $enNav);
        $this->assertEquals('Home EN', $enNav[0]['label']);
        $this->assertEquals('#home', $enNav[0]['anchor']);
    }

    public function test_rich_hero_and_cta_formatting_survives_english_save_and_publish_cycle()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $richSections = [
            [
                'type' => 'hero',
                'data' => [
                    'banners' => [
                        [
                            'title' => 'English Rich Hero Title',
                            'description' => '<p>English paragraph 1 with <strong>bold</strong>.</p><p><a href="https://madeena.co.id/en">English link</a></p>',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'cta',
                'data' => [
                    'title' => 'English Rich CTA Title',
                    'subtitle' => '<ul><li>English Benefit 1</li><li>English Benefit 2</li></ul>',
                    'button_text' => 'Contact Us',
                    'button_url' => '#contact',
                ],
            ],
        ];

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'en')
            ->fillForm(['sections' => $richSections])
            ->call('save')
            ->assertHasNoFormErrors();

        $enDraft = Setting::getJson('homepage_sections_en_draft');
        $this->assertEquals('<p>English paragraph 1 with <strong>bold</strong>.</p><p><a href="https://madeena.co.id/en">English link</a></p>', $enDraft[0]['data']['banners'][0]['description']);
        $this->assertStringContainsString('English Benefit 1', $enDraft[1]['data']['subtitle']);

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'en')
            ->call('publish');

        $enPublished = Setting::getJson('homepage_sections_en');
        $this->assertEquals('<p>English paragraph 1 with <strong>bold</strong>.</p><p><a href="https://madeena.co.id/en">English link</a></p>', $enPublished[0]['data']['banners'][0]['description']);
        $this->assertStringContainsString('English Benefit 1', $enPublished[1]['data']['subtitle']);
    }

    public function test_invalid_language_falls_back_safely()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Setting::setJson('homepage_sections', $this->sampleHeroSection('ID Hero'));

        $component = Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->call('switchLanguage', 'fr');

        $this->assertEquals('id', $component->get('activeLocale'));

        $this->assertEquals('homepage_sections', Setting::homepagePublishedKey('invalid_lang'));
        $this->assertEquals('homepage_sections_draft', Setting::homepageDraftKey('../../traversal'));
    }
}
