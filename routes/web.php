<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QrcodeController;
use App\Events\OrderEvent;
use App\Http\Controllers\OrderExportController;
Route::get('/', [HomeController::class, "index"]);
Route::get('/cart', [HomeController::class, "cart"]);
Route::middleware(['guest'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/login-admin', [AuthController::class, 'index'])->name('login-admin');
});
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'view']);
    Route::get('/data', [CartController::class, 'getData']);
    Route::get('/history', [CartController::class, 'getHistory']);
    Route::post('/add', [CartController::class, 'addOrUpdate']);
    Route::post('/checkout', [CartController::class, 'placeOrder']);
    Route::delete('/remove/{menu_id}', [CartController::class, 'remove']);
    Route::delete('/clear', [CartController::class, 'clear']);
});
Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->group(function(){
        Route::redirect('/', '/admin/dashboard');
        Route::get('dashboard', [DashboardController::class, 'index']);
        Route::get('header', [HomeController::class, 'headerView']);
        Route::get('footer', [HomeController::class, 'footerView']);
        Route::get('order', [OrderController::class, 'orderView']);
        Route::get('cashier', [OrderController::class, 'cashierView']);
        Route::get('history', [OrderController::class, 'reportView']);
        Route::get('menu', [MenuController::class, 'menuView']);
        Route::get('category', [MenuController::class, 'categoryView']);
        Route::get('qrcode', [QrcodeController::class, 'index']);
        Route::get('user', [UserController::class, 'userView']);
        Route::get('option', [MenuController::class, 'optionView']);
    });
    Route::prefix('order')->group(function () {
        Route::get('/data', [OrderController::class, 'dataOrder']);
        Route::get('/data/info', [OrderController::class, 'dataOrderInfo']);
        Route::get('/data-report', [OrderController::class, 'dataReport']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
    });
});

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return response()->json([
        'redirect' => url('/login-admin')
    ]);
})->name('logout');

Route::get('/export/order/download-report', [OrderExportController::class, 'download']);