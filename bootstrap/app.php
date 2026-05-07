<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (HttpException $exception, Request $request) {
            if ($exception->getStatusCode() !== 419 || ! $request->isMethod('post') || ! $request->is('inabuyer2026/feedback')) {
                return null;
            }

            return redirect()
                ->route('inabuyer2026.feedback')
                ->withInput($request->except('_token'))
                ->withErrors([
                    '_token' => 'Sesi formulir kedaluwarsa. Silakan kirim ulang formulir.',
                ]);
        });
    })->create();
