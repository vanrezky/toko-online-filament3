<?php

use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\Auth\ForgotPasswordController;
use App\Http\Controllers\Frontend\Auth\LoginController;
use App\Http\Controllers\Frontend\Auth\RegisterController;
use App\Http\Controllers\Frontend\HomeController;
use App\Livewire\AboutPage;
use App\Livewire\Auth\ForgotPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPage;
use App\Livewire\BlogPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CheckoutPage;
use App\Livewire\ContactPage;
use App\Livewire\HomePage;
use App\Livewire\MyOrderPage;
use App\Livewire\OrderDetailPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\ProductsPage;
use App\Livewire\ProfilePage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

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


Route::get('/', [HomeController::class, 'index']);

Route::middleware('guest')->group(function () {
    Route::get('/sign-in', LoginController::class)->name('login');
    Route::get('/sign-up', RegisterController::class);
    Route::get('/forgot-password', ForgotPasswordController::class);
});
Route::get('/account', AccountController::class)->middleware('auth');

// Route::get('/', HomePage::class)->name('home');
// Route::get('/products', ProductsPage::class);
// Route::get('/products/{product}', ProductDetailPage::class);

// Route::get('/checkout', CheckoutPage::class);
// Route::get('/my-orders', MyOrderPage::class);
// Route::get('my-orders/{order}', OrderDetailPage::class);

// Route::get('/contact', ContactPage::class);
// Route::get('/about', AboutPage::class);
// Route::get('/blog', BlogPage::class);

// Route::group(['middleware' => 'auth.customer.guest'], function () {
//     Route::get('/login', LoginPage::class)->name('login');
//     Route::get('/register', RegisterPage::class);
//     Route::get('/reset', ResetPage::class);
//     Route::get('/forgot', ForgotPage::class);
// });

// Route::group(['middleware' => 'auth.customer'], function () {
//     Route::get('/cart', CartPage::class);
//     Route::get('/order/success', SuccessPage::class);
//     Route::get('/order/cancel', CancelPage::class);
//     Route::get('/profile', ProfilePage::class);
// });
