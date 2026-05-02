<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

// ═══════════════════════════════════════════════════════════════════
// HOME
// ═══════════════════════════════════════════════════════════════════
Route::get('/', [HomeController::class, 'index'])->name('home');

// ═══════════════════════════════════════════════════════════════════
// CATÁLOGO DE LIBROS
// ═══════════════════════════════════════════════════════════════════
Route::prefix('books')->name('books.')->group(function () {
    Route::get('/',        [BookController::class, 'index'])->name('index');
    Route::get('/search',  [BookController::class, 'search'])->name('search');
    Route::get('/{slug}',  [BookController::class, 'show'])->name('show');
});

// ═══════════════════════════════════════════════════════════════════
// CARRITO (público + autenticado)
// ═══════════════════════════════════════════════════════════════════
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',                           [CartController::class, 'index'])->name('index');
    Route::post('/items',                     [CartController::class, 'addItem'])->name('add');
    Route::delete('/items/{bookId}/{format}', [CartController::class, 'removeItem'])->name('remove');
    Route::patch('/items/{bookId}/{format}',  [CartController::class, 'updateQuantity'])->name('update');
    Route::post('/coupon',                    [CartController::class, 'applyCoupon'])->name('coupon');
});

// ═══════════════════════════════════════════════════════════════════
// CHECKOUT — requiere auth + email verificado
// ═══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/',                    [CheckoutController::class, 'address'])->name('address');
        Route::post('/',                   [CheckoutController::class, 'storeAddress'])->name('store-address');
        Route::get('/payment',             [CheckoutController::class, 'payment'])->name('payment');
        Route::post('/intent',             [CheckoutController::class, 'createIntent'])->name('intent');
        Route::post('/confirm',            [CheckoutController::class, 'confirm'])->name('confirm');
        Route::get('/success/{order}',     [CheckoutController::class, 'success'])->name('success');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',        [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    });

    Route::prefix('downloads')->name('downloads.')->group(function () {
        Route::get('/',             [DownloadController::class, 'index'])->name('index');
        Route::get('/file/{token}', [DownloadController::class, 'download'])->name('file');
    });
});

// ═══════════════════════════════════════════════════════════════════
// PANEL ADMIN
// ═══════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                           [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/books',                      [AdminDashboardController::class, 'books'])->name('books');
    Route::patch('/books/{book}/stock',       [AdminDashboardController::class, 'updateBookStock'])->name('books.stock');
    Route::get('/orders',                     [AdminDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}',             [AdminDashboardController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status',    [AdminDashboardController::class, 'updateOrderStatus'])->name('orders.status');
    Route::get('/users',                      [AdminDashboardController::class, 'users'])->name('users');
});

// ═══════════════════════════════════════════════════════════════════
// WEBHOOK STRIPE — sin CSRF
// ═══════════════════════════════════════════════════════════════════
Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle'])
     ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
     ->name('webhooks.stripe');
