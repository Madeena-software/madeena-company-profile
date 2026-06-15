<?php

use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('madeena:seed-cms {--force}', function () {
    $cmsHasContent = Product::query()->exists()
        || Post::query()->exists()
        || Page::query()->exists()
        || Setting::query()->exists();

    if ($cmsHasContent) {
        $this->info('CMS content already exists; skipping seed.');

        return 0;
    }

    $this->call('db:seed', [
        '--force' => true,
    ]);

    $this->info('CMS content seeded.');

    return 0;
})->purpose('Seed the initial CMS content when the website tables are empty');
