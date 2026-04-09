<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Setting;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);

        if (app()->environment('production')) {
            $appUrl = config('app.url');

            if (! empty($appUrl)) {
                URL::forceRootUrl($appUrl);

                $scheme = parse_url($appUrl, PHP_URL_SCHEME);

                if (! empty($scheme)) {
                    URL::forceScheme($scheme);
                }
            }
        }

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
    }
}
