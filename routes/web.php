<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Inabuyer2026\FeedbackController;
use App\Http\Controllers\PublicStorageController;
use App\Livewire\Inabuyer2026Display;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();

        return response()->json(['status' => 'ok', 'db' => 'connected', 'timestamp' => now()]);
    } catch (Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blog', [HomeController::class, 'blog'])->name('blog.index');
Route::get('/produk/{product:slug}', [HomeController::class, 'product'])->name('product.show');
Route::get('/blog/{post:slug}', [HomeController::class, 'post'])->name('post.show');
Route::get('/storage/{path}', PublicStorageController::class)
    ->where('path', '.*')
    ->name('storage.public');

Route::prefix('inabuyer2026')
    ->name('inabuyer2026.')
    ->group(function (): void {
        Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback');
        Route::get('/feedback/csrf-token', [FeedbackController::class, 'csrfToken'])
            ->name('feedback.csrf-token');
        Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
        Route::get('/display', Inabuyer2026Display::class)->name('display');
    });
