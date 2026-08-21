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
}
