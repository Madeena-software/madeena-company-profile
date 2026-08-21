<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Models\Event as EventModel;
use App\Models\Post;
use App\Models\Setting;
use App\Policies\PostPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\LaravelPassport\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            LogoutResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event): void {
            $event->extendSocialite('laravelpassport', Provider::class);
        });

        Gate::policy(Post::class, PostPolicy::class);

        View::composer(['layouts.app', 'home', 'product', 'post'], function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $settings = Setting::all()->pluck('value', 'key');
                } else {
                    $settings = collect();
                }
            } catch (\Exception $e) {
                $settings = collect();
            }

            $view->with('settings', $settings);
        });

        RateLimiter::for('event-feedback-submission', function (Request $request) {
            $event = $request->route('event');
            $eventKey = $event instanceof EventModel ? (string) $event->id : (string) ($event ?? 'unknown');
            $ip = $request->ip() ?? '127.0.0.1';

            $limits = [
                Limit::perMinute(30)->by("event-feedback:post:ip:{$eventKey}:{$ip}"),
            ];

            $email = strtolower(trim((string) $request->input('email', '')));
            $phone = trim(preg_replace('/\D+/', '', (string) $request->input('phone', '')));
            $name = trim(preg_replace('/\s+/u', ' ', (string) $request->input('name', '')));
            $org = trim(preg_replace('/\s+/u', ' ', (string) $request->input('organization', '')));

            $contactIdentifier = null;
            if ($email !== '') {
                $contactIdentifier = "email:{$email}";
            } elseif ($phone !== '') {
                $contactIdentifier = "phone:{$phone}";
            } elseif ($name !== '' || $org !== '') {
                $contactIdentifier = 'contact:'.md5("{$name}:{$org}");
            }

            if ($contactIdentifier !== null) {
                $limits[] = Limit::perMinutes(10, 3)->by("event-feedback:post:contact:{$eventKey}:{$contactIdentifier}");
            }

            return $limits;
        });

        RateLimiter::for('event-feedback-csrf', function (Request $request) {
            $event = $request->route('event');
            $eventKey = $event instanceof EventModel ? (string) $event->id : (string) ($event ?? 'unknown');
            $ip = $request->ip() ?? '127.0.0.1';

            return Limit::perMinute(60)->by("event-feedback:csrf:{$eventKey}:{$ip}");
        });
    }
}
