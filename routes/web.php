<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'ok', 'db' => 'connected', 'timestamp' => now()]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog.index');
Route::get('/produk/{product:slug}', [HomeController::class, 'product'])->name('product.show');
Route::get('/blog/{post:slug}', [HomeController::class, 'post'])->name('post.show');
