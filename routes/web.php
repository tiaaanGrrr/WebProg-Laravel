<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminOrderReportController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminSessionController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\CustomerRegistrationController;
use App\Http\Controllers\CustomerSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', HomeController::class)->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->whereNumber('product')->name('products.show');
Route::post('/checkout/preview', [CheckoutController::class, 'preview'])->name('checkout.preview');
Route::get('/login', [CustomerSessionController::class, 'create'])->name('customer.login');
Route::post('/login', [CustomerSessionController::class, 'store'])->middleware('throttle:5,1')->name('customer.login.store');
Route::get('/register', [CustomerRegistrationController::class, 'create'])->name('customer.register');
Route::post('/register', [CustomerRegistrationController::class, 'store'])->middleware('throttle:5,1')->name('customer.register.store');
Route::middleware(['auth:customer', 'can:shop'])->group(function (): void {
    Route::post('/logout', [CustomerSessionController::class, 'destroy'])->name('customer.logout');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:20,1')->name('checkout.store');
    Route::get('/orders/{order:reference}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order:reference}', [CustomerOrderController::class, 'show'])->name('orders.show');
});

Route::get('/uploads/products/{filename}', function (string $filename) {
    abort_unless(Storage::disk('local')->exists('products/'.$filename), 404);

    return Storage::disk('local')->response('products/'.$filename, null, ['X-Content-Type-Options' => 'nosniff']);
})->where('filename', '[a-zA-Z0-9]+\.(jpg|jpeg|png|webp)')->name('products.image');

Route::prefix('admin')->group(function (): void {
    Route::get('/login', [AdminSessionController::class, 'create'])->name('login');
    Route::post('/login', [AdminSessionController::class, 'store'])->middleware('throttle:5,1')->name('admin.login');
    Route::middleware(['auth:web', 'can:manage-products'])->name('admin.')->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/reports/orders', [AdminOrderReportController::class, 'index'])->name('reports.orders');
        Route::get('/reports/orders/export', [AdminOrderReportController::class, 'export'])->name('reports.orders.export');
        Route::post('/logout', [AdminSessionController::class, 'destroy'])->name('logout');
        Route::resource('products', AdminProductController::class)->except('show');
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    });
});
