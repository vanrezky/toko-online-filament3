<?php

use App\Http\Controllers\Api\VoucherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public voucher routes
Route::prefix('vouchers')->group(function () {
    Route::get('/', [VoucherController::class, 'index']);
    Route::get('/available', [VoucherController::class, 'available']);
    Route::get('/{code}/validate', [VoucherController::class, 'validateCode']);
});

// Authenticated voucher routes
Route::middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    'auth.customer',
])->group(function () {
    Route::post('/vouchers/apply', [VoucherController::class, 'apply']);
    Route::post('/vouchers/remove', [VoucherController::class, 'remove']);
    Route::get('/vouchers/validate-cookie', [VoucherController::class, 'validateCookie']);
});
