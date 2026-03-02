<?php

use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;
use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductDetailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Global login route for Laravel default auth redirects
Route::get('/login', function () {
    return redirect()->route('frontend.login');
})->name('login');

Route::name('frontend.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Guest routes for customers
    Route::middleware('auth.customer.guest')->group(function () {
        Route::get('/login', LoginController::class)->name('login');
        Route::get('/register', RegisterController::class)->name('signup');
        Route::get('/forgot-password', ForgotPasswordController::class)->name('forgot-password');
    });

    // Authenticated routes for customers
    Route::get('/account', AccountController::class)->middleware('auth.customer')->name('account');
    Route::get('/checkout', \App\Http\Controllers\Frontend\CheckoutController::class)->middleware('auth.customer')->name('checkout');
    Route::get('/orders', [\App\Http\Controllers\Frontend\OrderController::class, 'index'])->middleware('auth.customer')->name('orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Frontend\OrderController::class, 'show'])->middleware('auth.customer')->name('orders.show');

    // Public shop routes
    Route::get('/cart', CartController::class)->name('cart');
    Route::get('/flash-sale', \App\Http\Controllers\Frontend\FlashsaleController::class)->name('flashsales');
    Route::get('/products', \App\Http\Controllers\Frontend\ProductController::class)->name('products');
    Route::get('/wishlist', [\App\Http\Controllers\Frontend\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Frontend\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    
    // Wildcard for product detail - MUST BE LAST
    Route::get('{product}', ProductDetailController::class)->name('product-detail');
});
