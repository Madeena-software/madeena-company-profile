<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SsoTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that the SSO redirect route redirects to the correct IAM endpoint.
     */
    public function test_sso_redirect_routes_to_iam(): void
    {
        // Mock Socialite redirect
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('with')
            ->with(['prompt' => 'login'])
            ->once()
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect()->to('http://localhost:8000/oauth/authorize?client_id=123'));

        Socialite::shouldReceive('driver')
            ->with('laravelpassport')
            ->once()
            ->andReturn($provider);

        $response = $this->get(route('sso.redirect'));
        $response->assertStatus(302);
        $response->assertRedirectContains('/oauth/authorize');
    }

    /**
     * Test that the silent redirect includes prompt=none.
     */
    public function test_sso_silent_redirect_includes_prompt_none(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('with')
            ->with(['prompt' => 'none'])
            ->once()
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect()->to('http://localhost:8000/oauth/authorize?client_id=123&prompt=none'));

        Socialite::shouldReceive('driver')
            ->with('laravelpassport')
            ->once()
            ->andReturn($provider);

        $response = $this->get(route('sso.silent'));
        $response->assertStatus(302);
        $response->assertRedirectContains('prompt=none');
    }

    /**
     * Test callback handling for login_required error.
     */
    public function test_callback_handles_login_required_by_redirecting_to_full_flow(): void
    {

        $response = $this->get(route('sso.callback', ['error' => 'login_required']));
        $response->assertStatus(302);
        $response->assertRedirect(route('filament.admin.auth.login'));
        $response->assertSessionHas('sso_silent_failed', true);
    }

    /**
     * Test callback handling for access_denied error.
     */
    public function test_callback_handles_access_denied_by_showing_error_view(): void
    {
        $response = $this->get(route('sso.callback', ['error' => 'access_denied']));
        $response->assertStatus(200);
        $response->assertSee('Akses Ditangguhkan');
        $response->assertSee('Akun Anda belum disetujui');
    }

    /**
     * Test successful SSO callback login flow.
     */
    public function test_successful_sso_callback_logs_in_user_and_calls_link_api(): void
    {
        // Mock IAM Link API
        config(['services.laravelpassport.host' => 'http://localhost:8000']);
        Http::fake([
            'http://localhost:8000/api/v1/client-user/link' => Http::response([], 200),
        ]);

        // Mock Socialite User
        $socialiteUser = Mockery::mock('Laravel\Socialite\Two\User');
        $socialiteUser->shouldReceive('getId')->andReturn('iam-user-id-123');
        $socialiteUser->shouldReceive('getName')->andReturn('Jane Doe');
        $socialiteUser->shouldReceive('getEmail')->andReturn('jane.doe@example.com');
        $socialiteUser->token = 'mock-bearer-token';

        $provider = Mockery::mock('Laravel\Socialite\Two\AbstractProvider');
        $provider->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('laravelpassport')
            ->once()
            ->andReturn($provider);

        $response = $this->get(route('sso.callback'));

        // Assert redirect to admin panel
        $response->assertRedirect('/admin');

        // Assert user was created
        $this->assertDatabaseHas('users', [
            'email' => 'jane.doe@example.com',
            'sso_id' => 'iam-user-id-123',
            'role' => 'user',
            'is_admin' => 0,
        ]);

        // Assert user is logged in
        $this->assertTrue(Auth::check());
        $this->assertEquals('jane.doe@example.com', Auth::user()->email);

        // Verify IAM Link API was called
        Http::assertSent(function ($request) {
            return $request->url() === 'http://localhost:8000/api/v1/client-user/link'
                && $request->method() === 'PATCH'
                && $request->hasHeader('Authorization', 'Bearer mock-bearer-token')
                && $request['client_app_user_id'] === Auth::user()->id;
        });
    }
}
