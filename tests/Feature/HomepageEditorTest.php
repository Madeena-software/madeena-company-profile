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

    public function test_homepage_editor_can_render_for_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(HomepageEditor::getUrl())->assertSuccessful();
    }

    public function test_homepage_editor_can_save_settings()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $sectionsData = [
            [
                'type' => 'hero',
                'data' => [
                    'show_in_nav' => true,
                    'nav_label' => 'Beranda',
                    'banners' => [
                        [
                            'title' => 'Welcome',
                            'subtitle' => 'To our site',
                        ],
                    ],
                ],
            ],
        ];

        Livewire::actingAs($admin)
            ->test(HomepageEditor::class)
            ->fillForm(['sections' => $sectionsData])
            ->call('save')
            ->assertHasNoFormErrors();

        $saved = Setting::getJson('homepage_sections');
        $this->assertIsArray($saved);
        $this->assertEquals('hero', $saved[0]['type']);
        $this->assertEquals('Welcome', $saved[0]['data']['banners'][0]['title']);
    }
}
