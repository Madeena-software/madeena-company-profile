<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GuestMessage;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->event = Event::create(['name' => 'Inabuyer 2026', 'slug' => 'inabuyer-2026', 'is_active' => true]);
    }

    public function test_feedback_page_is_accessible(): void
    {
        $response = $this->get(route('events.feedback', ['event' => $this->event->slug]));

        $response->assertOk();
        $response->assertSee('Jabatan');
        $response->assertSee('Nomor yang bisa');
        $response->assertSee('Email');
        $response->assertSee('data-feedback-form', false);
        $response->assertSee('data-csrf-refresh-url', false);
        $response->assertSee(route('events.feedback.csrf-token', ['event' => $this->event->slug]), false);
    }

    public function test_feedback_csrf_token_endpoint_returns_uncached_token(): void
    {
        $response = $this->getJson(route('events.feedback.csrf-token', ['event' => $this->event->slug]));

        $response->assertOk();
        $response->assertJsonStructure(['token']);

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertIsString($response->json('token'));
        $this->assertNotSame('', $response->json('token'));
    }

    public function test_feedback_submission_is_stored_successfully(): void
    {
        $payload = [
            'name' => 'Aisyah Putri',
            'organization' => 'PT Nusantara Export',
            'position' => 'Business Development Manager',
            'phone' => '+62 812 3456 7890',
            'email' => 'aisyah.putri@example.com',
            'kesan_dan_pesan' => 'Acara sangat bermanfaat dan saya berharap sesi networking ditambah pada tahun berikutnya.',
        ];

        $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('guest_messages', [
            'event_id' => $this->event->id,
            'name' => 'Aisyah Putri',
            'organization' => 'PT Nusantara Export',
            'position' => 'Business Development Manager',
            'phone' => '+62 812 3456 7890',
            'email' => 'aisyah.putri@example.com',
            'kesan_dan_pesan' => 'Acara sangat bermanfaat dan saya berharap sesi networking ditambah pada tahun berikutnya.',
            'is_visible' => true,
        ]);
    }

    public function test_feedback_submission_requires_kesan_dan_pesan(): void
    {
        $payload = [
            'name' => 'Bima Arta',
            'organization' => 'PT Mitra Dagang',
            'position' => 'Purchasing Lead',
            'phone' => '+62 813 5555 0000',
            'email' => 'bima.arta@example.com',
            'kesan_dan_pesan' => '',
        ];

        $response = $this->from(route('events.feedback', ['event' => $this->event->slug]))
            ->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHasErrors(['kesan_dan_pesan']);

        $this->assertSame(0, GuestMessage::query()->count());
    }

}
