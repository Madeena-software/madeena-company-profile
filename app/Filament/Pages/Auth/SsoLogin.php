<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class SsoLogin extends BaseLogin
{
    /**
     * Override mount method to redirect to SSO silent login.
     */
    public function mount(): void
    {
        parent::mount();

        // Redirect immediately to silent SSO flow
        redirect()->route('sso.silent');
    }

    /**
     * Override view to use a clean SSO redirect screen.
     */
    public function getView(): string
    {
        return 'filament.pages.auth.sso-login';
    }
}
