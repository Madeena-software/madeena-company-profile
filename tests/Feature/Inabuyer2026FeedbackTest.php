<?php

namespace Tests\Feature;

use App\Models\InabuyerMessage;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Testing\TestResponse;
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
        $response->assertSee('data-feedback-form', false);
        $response->assertSee('data-csrf-refresh-url', false);
        $response->assertSee(route('inabuyer2026.feedback.csrf-token'), false);
    }

    public function test_feedback_csrf_token_endpoint_returns_uncached_token(): void
    {
        $response = $this->getJson(route('inabuyer2026.feedback.csrf-token'));

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

    public function test_feedback_token_mismatch_redirects_back_with_input_and_error(): void
    {
        $payload = [
            'name' => 'Citra Lestari',
            'organization' => 'RS Sehat Sentosa',
            'position' => 'Radiology Coordinator',
            'phone' => '+62 811 2222 3333',
            'email' => 'citra.lestari@example.com',
            'kesan_dan_pesan' => 'Formulir ini tetap harus bisa dikirim ulang saat sesi kedaluwarsa.',
            '_token' => 'expired-token',
        ];

        $session = $this->app['session.store'];
        $session->start();

        $request = Request::create('/inabuyer2026/feedback', 'POST', $payload);
        $request->setLaravelSession($session);

        $response = $this->app->make(ExceptionHandler::class)
            ->render($request, new TokenMismatchException('CSRF token mismatch.'));

        $testResponse = TestResponse::fromBaseResponse($response);

        $testResponse->assertRedirect(route('inabuyer2026.feedback'));
        $testResponse->assertSessionHasErrors(['_token']);
        $testResponse->assertSessionHasInput('name', 'Citra Lestari');
        $testResponse->assertSessionHasInput('organization', 'RS Sehat Sentosa');

        $this->assertFalse($session->hasOldInput('_token'));
    }
}
