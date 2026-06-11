<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class SsoLogin extends BaseLogin
{
    public function mount(): void
    {
        if (\Filament\Facades\Filament::auth()->check()) {
            redirect()->intended(\Filament\Facades\Filament::getUrl());
            return;
        }

        if (! session()->has('sso_silent_failed') && ! session()->has('sso_manual_login')) {
            redirect()->route('sso.silent');
            return;
        }

        $this->form->fill();
    }

    /**
     * Override view to use a clean SSO redirect screen.
     */
    public function getView(): string
    {
        return 'filament.pages.auth.sso-login';
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Masuk ke Dasbor';
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Gunakan akun sentral Anda untuk mengakses aplikasi ini.';
    }
}
