<?php

namespace Tests\Feature;

use App\Models\InabuyerMessage;
use Tests\TestCase;

class Inabuyer2026FeedbackTest extends TestCase
{
    public function test_feedback_page_is_accessible(): void
    {
        $response = $this->get(route('inabuyer2026.feedback'));

        $response->assertOk();
        $response->assertSee('Inabuyer 2026 Feedback');
        $response->assertSee('Jabatan');
        $response->assertSee('Nomor yang bisa');
        $response->assertSee('Email');
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

        $response = $this->post(route('inabuyer2026.feedback.store'), $payload);

        $response->assertRedirect(route('inabuyer2026.feedback'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('inabuyer_messages', [
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

        $response = $this->from(route('inabuyer2026.feedback'))
            ->post(route('inabuyer2026.feedback.store'), $payload);

        $response->assertRedirect(route('inabuyer2026.feedback'));
        $response->assertSessionHasErrors(['kesan_dan_pesan']);

        $this->assertSame(0, InabuyerMessage::query()->count());
    }
}
