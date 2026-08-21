<?php

namespace App\Http\Controllers;

use App\Filament\Pages\HomepageEditor;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        return $this->renderHomepage('id');
    }

    public function indexEn()
    {
        return $this->renderHomepage('en');
    }

    protected function renderHomepage(string $locale = 'id')
    {
        $locale = Setting::normalizeLocale($locale);
        app()->setLocale($locale);

        $user = \Illuminate\Support\Facades\Auth::user();
        $isPreview = request('preview') === 'true' && $user instanceof \App\Models\User && $user->isAdmin();

        $sections = Setting::getHomepageSections($locale, $isPreview);
        $seo         = Setting::getJson('seo', []);
        $contactInfo = Setting::getJson('contact_info', []);
        $socialMedia = Setting::getJson('social_media', []);
        $branding    = Setting::getJson('branding', []);
        $whatsapp    = Setting::getJson('whatsapp_button', ['enabled' => true, 'number' => '']);
        $navItems    = HomepageEditor::getNavigation($isPreview, $locale);

        // Inject dynamic data into auto-pull sections
        foreach ($sections as &$section) {
            match ($section['type'] ?? '') {
                'products' => $section['products'] = Product::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get(),
                'artikel' => $section['posts'] = Post::where('is_published', true)
                    ->when($locale === 'id', function ($query) {
                        return $query->where(function ($q) {
                            $q->where('content_language', 'id')
                              ->orWhereNull('content_language');
                        });
                    }, function ($query) use ($locale) {
                        return $query->where('content_language', $locale);
                    })
                    ->when(!empty($section['data']['category_filter']), function ($query) use ($section) {
                        return $query->where('placement', $section['data']['category_filter']);
                    })
                    ->orderByDesc('published_at')
                    ->take((int) ($section['data']['posts_count'] ?? 3))
                    ->get(),
                'contact' => $section['contact'] = $contactInfo,
                'about' => $section['page'] = \App\Models\Page::find($section['data']['page_id'] ?? null),
                default => null,
            };
        }
        unset($section);

        return view('home', compact(
            'sections',
            'seo',
            'contactInfo',
            'socialMedia',
            'branding',
            'whatsapp',
            'navItems',
            'isPreview',
            'locale'
        ));
    }

    public function artikel()
    {
        $posts = Post::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('artikel', compact('posts'));
    }

    public function post(Post $post)
    {
        abort_if(! $post->is_published, 404);

        return view('post', compact('post'));
    }

    public function product(Product $product)
    {
        abort_if(! $product->is_active, 404);

        return view('product', compact('product'));
    }

    public function page(\App\Models\Page $page)
    {
        return view('page', compact('page'));
    }
}
