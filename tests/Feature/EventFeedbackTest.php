<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GuestMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class EventFeedbackTest extends TestCase
{
    use RefreshDatabase;

    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->event = Event::create(['name' => 'Inabuyer 2026', 'slug' => 'inabuyer-2026', 'is_active' => true]);
    }

    public function test_feedback_page_is_accessible_for_active_event(): void
    {
        $response = $this->get(route('events.feedback', ['event' => $this->event->slug]));

        $response->assertOk();
        $response->assertSee('Jabatan');
        $response->assertSee('Nomor yang bisa');
        $response->assertSee('Email');
        $response->assertSee('data-feedback-form', false);
        $response->assertSee('data-csrf-refresh-url', false);
        $response->assertSee(route('events.feedback.csrf-token', ['event' => $this->event->slug]), false);
        $response->assertSee('name="website"', false);
    }

    public function test_feedback_csrf_token_endpoint_returns_uncached_token_for_active_event(): void
    {
        $response = $this->getJson(route('events.feedback.csrf-token', ['event' => $this->event->slug]));

        $response->assertOk();
        $response->assertJsonStructure(['token']);

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertIsString($response->json('token'));
        $this->assertNotSame('', $response->json('token'));
    }

    public function test_inactive_event_returns_404_for_get_csrf_and_post(): void
    {
        $inactiveEvent = Event::create([
            'name' => 'Past Exhibition',
            'slug' => 'past-exhibition',
            'is_active' => false,
        ]);

        $this->get(route('events.feedback', ['event' => $inactiveEvent->slug]))
            ->assertNotFound();

        $this->getJson(route('events.feedback.csrf-token', ['event' => $inactiveEvent->slug]))
            ->assertNotFound();

        $payload = [
            'name' => 'Doni Kusuma',
            'organization' => 'PT Sumber Rejeki',
            'kesan_dan_pesan' => 'Mencoba submit ke event non-aktif.',
        ];

        $this->post(route('events.feedback.store', ['event' => $inactiveEvent->slug]), $payload)
            ->assertNotFound();

        $this->assertSame(0, GuestMessage::query()->count());
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

    public function test_honeypot_silently_discards_submission_without_creating_guest_message(): void
    {
        $spamPayload = [
            'name' => 'Spam Bot',
            'organization' => 'Bot Net Ltd',
            'email' => 'bot@spammer.example',
            'kesan_dan_pesan' => 'Buy cheap meds at https://spam.example',
            'website' => 'https://spam.example',
        ];

        $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $spamPayload);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHas('success');
        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(0, GuestMessage::query()->count());

        $humanPayload = [
            'name' => 'Human Visitor',
            'organization' => 'RS Harapan',
            'email' => 'human@harapan.example',
            'kesan_dan_pesan' => 'Booth sangat rapi dan informatif.',
            'website' => '',
        ];

        $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $humanPayload);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHas('success');
        $this->assertSame(1, GuestMessage::query()->count());
    }

    public function test_duplicate_submission_within_window_is_suppressed_for_same_event(): void
    {
        $payload = [
            'name' => 'Citra Dewi',
            'organization' => 'PT Medika Utama',
            'position' => 'Procurement Officer',
            'phone' => '081299998888',
            'email' => 'citra@medika.example',
            'kesan_dan_pesan' => 'Demo alat rontgen digital sangat impresif!',
        ];

        $firstResponse = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);
        $firstResponse->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $firstResponse->assertSessionHas('success');
        $this->assertSame(1, GuestMessage::query()->count());

        // Immediate repeat submission with identical payload
        $secondResponse = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);
        $secondResponse->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $secondResponse->assertSessionHas('success');
        $this->assertSame(1, GuestMessage::query()->count());

        // Submission with modified message text is allowed as distinct row
        $modifiedPayload = array_merge($payload, [
            'kesan_dan_pesan' => 'Tambahan catatan: kami berminat untuk demo di rumah sakit kami.',
        ]);

        $thirdResponse = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $modifiedPayload);
        $thirdResponse->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $thirdResponse->assertSessionHas('success');
        $this->assertSame(2, GuestMessage::query()->count());
    }

    public function test_input_normalization_collapses_whitespace_and_normalizes_email(): void
    {
        $firstPayload = [
            'name' => '  Aisyah Putri  ',
            'organization' => '  PT   Nusantara   Export  ',
            'position' => '  Business   Development  ',
            'phone' => '  +62 812 3456 7890  ',
            'email' => 'AISYAH@EXAMPLE.COM',
            'kesan_dan_pesan' => 'Acara   sangat   bagus',
        ];

        $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $firstPayload);
        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));

        $message = GuestMessage::query()->firstOrFail();
        $this->assertSame('Aisyah Putri', $message->name);
        $this->assertSame('PT Nusantara Export', $message->organization);
        $this->assertSame('Business Development', $message->position);
        $this->assertSame('+62 812 3456 7890', $message->phone);
        $this->assertSame('aisyah@example.com', $message->email);
        $this->assertSame('Acara sangat bagus', $message->kesan_dan_pesan);

        // Second submission with pre-normalized values should be recognized as duplicate
        $secondPayload = [
            'name' => 'Aisyah Putri',
            'organization' => 'PT Nusantara Export',
            'position' => 'Business Development',
            'phone' => '+62 812 3456 7890',
            'email' => 'aisyah@example.com',
            'kesan_dan_pesan' => 'Acara sangat bagus',
        ];

        $response2 = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $secondPayload);
        $response2->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $this->assertSame(1, GuestMessage::query()->count());
    }

    public function test_unicode_and_indonesian_characters_are_preserved(): void
    {
        $payload = [
            'name' => 'Dr. Bambang Tri-Atmojo, Sp.Rad & Rekan',
            'organization' => 'RSUD dr. Soetomo — Surabaya',
            'position' => 'Kepala Instalasi Radiologi',
            'phone' => '(031) 555-1234',
            'email' => 'bambang.rad@soetomo.example',
            'kesan_dan_pesan' => 'Inovasi CCXD buatan UGM & PT Madeena sangat membanggakan! Sukses selalu untuk karya anak bangsa.',
        ];

        $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);
        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));

        $message = GuestMessage::query()->firstOrFail();
        $this->assertSame('Dr. Bambang Tri-Atmojo, Sp.Rad & Rekan', $message->name);
        $this->assertSame('RSUD dr. Soetomo — Surabaya', $message->organization);
        $this->assertSame('Kepala Instalasi Radiologi', $message->position);
        $this->assertSame('Inovasi CCXD buatan UGM & PT Madeena sangat membanggakan! Sukses selalu untuk karya anak bangsa.', $message->kesan_dan_pesan);
    }

    public function test_duplicate_suppression_is_scoped_by_event(): void
    {
        $eventA = $this->event;
        $eventB = Event::create(['name' => 'Hospital Expo 2026', 'slug' => 'hospital-expo-2026', 'is_active' => true]);

        $payload = [
            'name' => 'Aisyah Putri',
            'organization' => 'PT Nusantara Export',
            'kesan_dan_pesan' => 'Selamat atas pamerannya!',
        ];

        $this->post(route('events.feedback.store', ['event' => $eventA->slug]), $payload)
            ->assertRedirect(route('events.feedback', ['event' => $eventA->slug]));

        $this->post(route('events.feedback.store', ['event' => $eventB->slug]), $payload)
            ->assertRedirect(route('events.feedback', ['event' => $eventB->slug]));

        $this->assertSame(1, $eventA->guestMessages()->count());
        $this->assertSame(1, $eventB->guestMessages()->count());
        $this->assertSame(2, GuestMessage::query()->count());
    }

    public function test_validation_rejects_missing_required_fields_and_invalid_email_and_overlength(): void
    {
        // Missing kesan_dan_pesan
        $payloadMissing = [
            'name' => 'Bima Arta',
            'organization' => 'PT Mitra Dagang',
            'kesan_dan_pesan' => '',
        ];

        $response = $this->from(route('events.feedback', ['event' => $this->event->slug]))
            ->post(route('events.feedback.store', ['event' => $this->event->slug]), $payloadMissing);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHasErrors(['kesan_dan_pesan']);
        $this->assertSame(0, GuestMessage::query()->count());

        // Invalid email format
        $payloadInvalidEmail = [
            'name' => 'Bima Arta',
            'organization' => 'PT Mitra Dagang',
            'email' => 'bima-not-an-email',
            'kesan_dan_pesan' => 'Pesan valid.',
        ];

        $response = $this->from(route('events.feedback', ['event' => $this->event->slug]))
            ->post(route('events.feedback.store', ['event' => $this->event->slug]), $payloadInvalidEmail);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHasErrors(['email']);
        $this->assertSame(0, GuestMessage::query()->count());

        // Overlength message (>5000 chars)
        $payloadOverLength = [
            'name' => 'Bima Arta',
            'organization' => 'PT Mitra Dagang',
            'kesan_dan_pesan' => str_repeat('A', 5001),
        ];

        $response = $this->from(route('events.feedback', ['event' => $this->event->slug]))
            ->post(route('events.feedback.store', ['event' => $this->event->slug]), $payloadOverLength);

        $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
        $response->assertSessionHasErrors(['kesan_dan_pesan']);
        $this->assertSame(0, GuestMessage::query()->count());
    }

    public function test_rate_limiter_throttles_excessive_post_submissions_by_ip(): void
    {
        RateLimiter::clear("event-feedback:post:ip:{$this->event->id}:127.0.0.1");

        // 30 requests with distinct contact fingerprints permitted under 30/min IP limit
        for ($i = 1; $i <= 30; $i++) {
            $payload = [
                'name' => "Rate Limit Test User {$i}",
                'organization' => "PT Benchmark {$i}",
                'email' => "user{$i}@benchmark.example",
                'kesan_dan_pesan' => "Pesan pengujian rate limiter unique {$i}",
            ];

            $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);
            $response->assertStatus(302);
        }

        $this->assertSame(30, GuestMessage::query()->count());

        // 31st request from same IP exceeds the 30/minute threshold -> 429 Too Many Requests
        $overflowPayload = [
            'name' => 'Rate Limit Test User 31',
            'organization' => 'PT Benchmark 31',
            'email' => 'user31@benchmark.example',
            'kesan_dan_pesan' => 'Pesan overflow melebihi limit',
        ];

        $blockedResponse = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $overflowPayload);
        $blockedResponse->assertStatus(429);

        // Ensure no extra DB record was created after throttling
        $this->assertSame(30, GuestMessage::query()->count());
    }

    public function test_rate_limiter_throttles_excessive_post_submissions_by_contact(): void
    {
        $contactEmail = 'frequent.sender@example.com';
        RateLimiter::clear("event-feedback:post:contact:{$this->event->id}:email:{$contactEmail}");
        RateLimiter::clear("event-feedback:post:ip:{$this->event->id}:127.0.0.1");

        // 3 submissions with different messages permitted under 3/10min contact limit
        for ($i = 1; $i <= 3; $i++) {
            $payload = [
                'name' => 'Frequent Sender',
                'organization' => 'PT Frequent',
                'email' => $contactEmail,
                'kesan_dan_pesan' => "Distinct message from frequent sender {$i}",
            ];

            $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $payload);
            $response->assertStatus(302);
        }

        $this->assertSame(3, GuestMessage::query()->count());

        // 4th submission within window for same contact is throttled with 429
        $overflowPayload = [
            'name' => 'Frequent Sender',
            'organization' => 'PT Frequent',
            'email' => $contactEmail,
            'kesan_dan_pesan' => '4th distinct message from frequent sender',
        ];

        $blockedResponse = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), $overflowPayload);
        $blockedResponse->assertStatus(429);

        $this->assertSame(3, GuestMessage::query()->count());
    }

    public function test_rate_limiter_throttles_excessive_csrf_token_requests(): void
    {
        RateLimiter::clear("event-feedback:csrf:{$this->event->id}:127.0.0.1");

        for ($i = 1; $i <= 60; $i++) {
            $response = $this->getJson(route('events.feedback.csrf-token', ['event' => $this->event->slug]));
            $response->assertOk();
        }

        $blockedResponse = $this->getJson(route('events.feedback.csrf-token', ['event' => $this->event->slug]));
        $blockedResponse->assertStatus(429);
    }

    public function test_shared_ip_booth_allows_multiple_distinct_visitors_below_threshold(): void
    {
        RateLimiter::clear("event-feedback:post:ip:{$this->event->id}:127.0.0.1");

        $visitors = [
            ['name' => 'Pengunjung 1', 'email' => 'p1@example.com', 'org' => 'RS A', 'msg' => 'Pesan pengunjung 1'],
            ['name' => 'Pengunjung 2', 'email' => 'p2@example.com', 'org' => 'RS B', 'msg' => 'Pesan pengunjung 2'],
            ['name' => 'Pengunjung 3', 'email' => 'p3@example.com', 'org' => 'RS C', 'msg' => 'Pesan pengunjung 3'],
            ['name' => 'Pengunjung 4', 'email' => 'p4@example.com', 'org' => 'RS D', 'msg' => 'Pesan pengunjung 4'],
            ['name' => 'Pengunjung 5', 'email' => 'p5@example.com', 'org' => 'RS E', 'msg' => 'Pesan pengunjung 5'],
        ];

        foreach ($visitors as $v) {
            $response = $this->post(route('events.feedback.store', ['event' => $this->event->slug]), [
                'name' => $v['name'],
                'organization' => $v['org'],
                'email' => $v['email'],
                'kesan_dan_pesan' => $v['msg'],
            ]);

            $response->assertRedirect(route('events.feedback', ['event' => $this->event->slug]));
            $response->assertSessionHas('success');
        }

        $this->assertSame(5, GuestMessage::query()->count());
    }

    public function test_feedback_page_preserves_indonesian_locale_under_ambient_english(): void
    {
        app()->setLocale('en');
        config(['app.locale' => 'en']);

        $response = $this->get(route('events.feedback', ['event' => $this->event->slug]));

        $response->assertOk();
        $response->assertSee('lang="id"', false);
        $response->assertSee('Navigasi');
        $response->assertSee('Kontak');
        $response->assertSee('Seluruh hak dilindungi.');
        $response->assertSee('Kesan dan Pesan Anda untuk Booth Madeena');
        $response->assertSee('Form Feedback Booth Madeena');
        $response->assertDontSee('data-testid="language-switcher-desktop"', false);
        $response->assertDontSee('data-testid="language-switcher-mobile"', false);
    }
}
