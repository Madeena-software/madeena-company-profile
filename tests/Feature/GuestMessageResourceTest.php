<?php

namespace Tests\Feature;

use App\Filament\Resources\GuestMessages\GuestMessageResource;
use App\Models\Event;
use App\Models\GuestMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestMessageResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_render_message_index()
    {
        $this->actingAs($this->admin)->get(GuestMessageResource::getUrl('index'))->assertSuccessful();
    }

    public function test_can_render_message_edit()
    {
        $event = Event::create(['name' => 'Test Event', 'slug' => 'test-event', 'is_active' => true]);
        
        $message = GuestMessage::create([
            'event_id' => $event->id,
            'name' => 'Test Name',
            'organization' => 'Test Company',
            'phone' => '081234567890',
            'email' => 'test@example.com',
            'kesan_dan_pesan' => 'Test Kesan'
        ]);

        $this->actingAs($this->admin)->get(GuestMessageResource::getUrl('edit', ['record' => $message]))->assertSuccessful();
    }
}
