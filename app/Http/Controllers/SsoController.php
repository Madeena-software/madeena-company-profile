<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class SsoController extends Controller
{
    /**
     * Redirect the user to the Madeena IAM authentication page.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('laravelpassport')
            ->with(['prompt' => 'login'])
            ->redirect();
    }

    /**
     * Redirect the user to the Madeena IAM authentication page silently.
     */
    public function silentRedirect(): RedirectResponse
    {
        return Socialite::driver('laravelpassport')
            ->with(['prompt' => 'none'])
            ->redirect();
    }

    /**
     * Handle the callback from Madeena IAM.
     */
    public function callback(Request $request): RedirectResponse|View
    {
        // 1. Handle OAuth Callback Errors
        $error = $request->query('error');
        if ($error) {
            if ($error === 'login_required') {
                // Silent auth failed (prompt=none), redirect to login page with flag
                return redirect()->route('filament.admin.auth.login')->with('sso_silent_failed', true);
            }

            if ($error === 'access_denied') {
                // User is not approved/blocked
                return view('errors.sso', [
                    'title' => 'Akses Ditangguhkan',
                    'message' => 'Akun Anda belum disetujui atau sedang ditangguhkan untuk mengakses aplikasi ini. Silakan hubungi administrator central IAM.',
                ]);
            }

            Log::warning('SSO Authentication failed with error: '.$error);

            return view('errors.sso', [
                'title' => 'Gagal Masuk',
                'message' => 'Terjadi kesalahan saat melakukan login dengan SSO: '.$error,
            ]);
        }

        try {
            // 2. Retrieve the user details and access token
            $socialiteUser = Socialite::driver('laravelpassport')->user();
            $token = $socialiteUser->token;

            if (empty($socialiteUser->getEmail())) {
                throw new Exception('Email tidak ditemukan dari respons identity provider.');
            }

            // 3. Find or create the local user by email
            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // Update sso_id if not set or changed
                if ($user->sso_id !== $socialiteUser->getId()) {
                    $user->sso_id = $socialiteUser->getId();
                    $user->save();
                }
            } else {
                // Auto-create new user
                $user = User::create([
                    'name' => $socialiteUser->getName() ?? explode('@', $socialiteUser->getEmail())[0],
                    'email' => $socialiteUser->getEmail(),
                    'sso_id' => $socialiteUser->getId(),
                    'role' => 'user',
                    'is_admin' => false,
                ]);
            }

            // 4. Call the IAM Link API (PATCH /api/v1/client-user/link)
            $iamUrl = rtrim(config('services.laravelpassport.host'), '/');
            if (! empty($iamUrl) && ! empty($token)) {
                try {
                    $response = Http::withToken($token)
                        ->timeout(5)
                        ->patch($iamUrl.'/api/v1/client-user/link', [
                            'client_app_user_id' => $user->id,
                        ]);

                    if ($response->failed()) {
                        Log::error('IAM link API failed for user '.$user->email.' (ID: '.$user->id.'): '.$response->body());
                    }
                } catch (Exception $e) {
                    Log::error('IAM link API exception: '.$e->getMessage());
                }
            }

            // 5. Log the user in locally
            Auth::login($user);

            // 6. Redirect to the admin dashboard
            return redirect()->intended('/admin');

        } catch (Exception $e) {
            Log::error('SSO Callback Exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return view('errors.sso', [
                'title' => 'Kesalahan Autentikasi',
                'message' => 'Terjadi kesalahan sistem saat memproses login Anda: '.$e->getMessage(),
            ]);
        }
    }
}
