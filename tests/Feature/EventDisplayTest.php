<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GuestMessage;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_display_shows_only_visible_messages_and_feedback_cta(): void
    {
        $event = Event::create(['name' => 'Inabuyer 2026', 'slug' => 'inabuyer-2026', 'is_active' => true]);

        GuestMessage::query()->create([
            'event_id' => $event->id,
            'name' => 'Visible Guest',
            'organization' => 'PT Terbuka',
            'kesan_dan_pesan' => 'Pesan ini harus tampil di display.',
            'is_visible' => true,
        ]);

        GuestMessage::query()->create([
            'event_id' => $event->id,
            'name' => 'Hidden Guest',
            'organization' => 'PT Disembunyikan',
            'kesan_dan_pesan' => 'Pesan ini tidak boleh tampil di display.',
            'is_visible' => false,
        ]);

        $response = $this->get(route('events.display', ['event' => $event->slug]));

        $response->assertOk();
        $response->assertSee('Visible Guest');
        $response->assertSee('Pesan ini harus tampil di display.');
        $response->assertDontSee('Hidden Guest');
        $response->assertDontSee('Pesan ini tidak boleh tampil di display.');
        $response->assertSee('https://bit.ly/madeenafeedback');
    }
}
