<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettings;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use DatabaseTruncation;

    public function test_site_settings_can_render_for_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(SiteSettings::getUrl())->assertSuccessful();
    }

    public function test_site_settings_can_save_data()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(SiteSettings::class)
            ->fillForm([
                'contact_info' => ['email' => 'test@example.com'],
                'seo' => ['meta_title' => 'Test SEO Title'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('test@example.com', Setting::getJson('contact_info')['email']);
        $this->assertEquals('Test SEO Title', Setting::getJson('seo')['meta_title']);
    }
}
