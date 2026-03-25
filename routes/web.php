<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk/{product:slug}', [HomeController::class, 'product'])->name('product.show');
Route::get('/blog/{post:slug}', [HomeController::class, 'post'])->name('post.show');
