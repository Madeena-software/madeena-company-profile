<?php

namespace App\Http\Controllers;

use App\Models\HeroBanner;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;

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

        return view('home', compact('banners', 'products', 'posts'));
    }

    public function blog()
    {
        $posts = Post::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('blog', compact('posts'));
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

    public function page(Page $page)
    {
        return view('page', compact('page'));
    }
}
