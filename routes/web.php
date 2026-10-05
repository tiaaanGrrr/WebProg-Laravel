<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// UI belum query database
Route::view('/', 'home')->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
