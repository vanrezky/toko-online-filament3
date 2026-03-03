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
        Route::post('/login', [LoginController::class, 'login'])->name('login.post');
        Route::get('/register', RegisterController::class)->name('signup');
        Route::post('/register', [RegisterController::class, 'register'])->name('signup.post');
        Route::get('/forgot-password', ForgotPasswordController::class)->name('forgot-password');
    });

    // Authenticated routes for customers
    Route::middleware('auth.customer')->group(function () {
        Route::get('/account', AccountController::class)->name('account');
        Route::post('/account/update', [AccountController::class, 'updateProfile'])->name('account.update');
        
        // Address management
        Route::post('/account/address', [AccountController::class, 'storeAddress'])->name('account.address.store');
        Route::patch('/account/address/{address}', [AccountController::class, 'updateAddress'])->name('account.address.update');
        Route::delete('/account/address/{address}', [AccountController::class, 'deleteAddress'])->name('account.address.delete');
        
        // Regions
        Route::get('/regions/districts/{province}', [AccountController::class, 'getDistricts'])->name('regions.districts');
        Route::get('/regions/sub-districts/{district}', [AccountController::class, 'getSubDistricts'])->name('regions.sub-districts');

        Route::post('/logout', \App\Http\Controllers\Frontend\Auth\LogoutController::class)->name('logout');
        Route::get('/checkout', \App\Http\Controllers\Frontend\CheckoutController::class)->name('checkout');
        Route::get('/orders', [\App\Http\Controllers\Frontend\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [\App\Http\Controllers\Frontend\OrderController::class, 'show'])->name('orders.show');
    });

    // Public shop routes
    Route::get('/cart', CartController::class)->name('cart');
    Route::get('/flash-sale', \App\Http\Controllers\Frontend\FlashsaleController::class)->name('flashsales');
    Route::get('/products', \App\Http\Controllers\Frontend\ProductController::class)->name('products');
    Route::get('/blog', [\App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('blog.index');
    Route::get('/blog/{slug}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('blog.show');
    Route::get('/page/{slug}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])->name('page.show');
    Route::get('/wishlist', [\App\Http\Controllers\Frontend\WishlistController::class, 'index'])->name('wishlist');
    Route::get('/faq', \App\Http\Controllers\Frontend\FaqController::class)->name('faq');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\Frontend\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    
    // Wildcard for product detail - MUST BE LAST
    Route::get('{product}', ProductDetailController::class)->name('product-detail');
});
