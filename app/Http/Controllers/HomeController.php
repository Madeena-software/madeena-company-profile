<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $banners = HeroBanner::where('is_active', true)->orderBy('sort_order')->get();
        $products = Product::where('is_active', true)->orderBy('sort_order')->get();
        $posts = Post::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();
        $settings = Setting::all()->pluck('value', 'key');

        return view('home', compact('banners', 'products', 'posts', 'settings'));
    }

    public function post(\App\Models\Post $post)
    {
        abort_if(! $post->is_published, 404);
        return view('post', compact('post'));
    }

    public function product(\App\Models\Product $product)
    {
        abort_if(! $product->is_active, 404);
        return view('product', compact('product'));
    }
}
