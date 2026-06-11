<?php

namespace Tests\Feature;

use App\Models\InabuyerMessage;
use Tests\TestCase;

class Inabuyer2026DisplayTest extends TestCase
{
    public function test_display_shows_only_visible_messages_and_feedback_cta(): void
    {
        InabuyerMessage::query()->create([
            'name' => 'Visible Guest',
            'organization' => 'PT Terbuka',
            'kesan_dan_pesan' => 'Pesan ini harus tampil di display.',
            'is_visible' => true,
        ]);

        InabuyerMessage::query()->create([
            'name' => 'Hidden Guest',
            'organization' => 'PT Disembunyikan',
            'kesan_dan_pesan' => 'Pesan ini tidak boleh tampil di display.',
            'is_visible' => false,
        ]);

        $response = $this->get(route('inabuyer2026.display'));

        $response->assertOk();
        $response->assertSee('Visible Guest');
        $response->assertSee('Pesan ini harus tampil di display.');
        $response->assertDontSee('Hidden Guest');
        $response->assertDontSee('Pesan ini tidak boleh tampil di display.');
        $response->assertSee('https://bit.ly/madeenafeedback');
    }
}
