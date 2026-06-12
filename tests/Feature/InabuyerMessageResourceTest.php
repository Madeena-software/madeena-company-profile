<?php

namespace Tests\Feature;

use App\Filament\Resources\InabuyerMessageResource;
use App\Models\InabuyerMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InabuyerMessageResourceTest extends TestCase
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
        $this->actingAs($this->admin)->get(InabuyerMessageResource::getUrl('index'))->assertSuccessful();
    }

    public function test_can_render_message_edit()
    {
        $message = InabuyerMessage::create([
            'name' => 'Test Name',
            'company' => 'Test Company',
            'phone' => '081234567890',
            'email' => 'test@example.com',
            'topic' => 'Kemitraan',
            'message' => 'Test Message',
            'kesan_dan_pesan' => 'Test Kesan'
        ]);

        $this->actingAs($this->admin)->get(InabuyerMessageResource::getUrl('edit', ['record' => $message]))->assertSuccessful();
    }
}
