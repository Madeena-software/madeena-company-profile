<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Setting;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
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

        View::composer(['layouts.app', 'home', 'product', 'post', 'page'], function ($view) {
            try {
                $settings = Schema::hasTable('settings') ? Setting::all()->pluck('value', 'key') : collect();

                $headerMenus = Schema::hasTable('menu_items')
                    ? \App\Models\MenuItem::where('is_active', true)->whereIn('location', ['header', 'both'])->orderBy('sort_order')->get()
                    : collect();

                $footerMenus = Schema::hasTable('menu_items')
                    ? \App\Models\MenuItem::where('is_active', true)->whereIn('location', ['footer', 'both'])->orderBy('sort_order')->get()
                    : collect();
            } catch (\Exception $e) {
                $settings = collect();
                $headerMenus = collect();
                $footerMenus = collect();
            }

            $view->with(compact('settings', 'headerMenus', 'footerMenus'));
        });
    }
}
