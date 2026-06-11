<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        // Set the session flag to prevent auto silent auth after explicit logout
        $request->session()->put('sso_manual_login', true);

        return redirect()->route('filament.admin.auth.login');
    }
}
