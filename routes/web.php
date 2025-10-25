<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DashboardController;
use App\Events\OrderEvent;

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
// broadcast(new OrderEvent("dari Azka Ainurridho")); // => show notif
Route::get('/', [HomeController::class, "index"]);
Route::get('/cart', [HomeController::class, "cart"]);
Route::post('/broadcast-order', [OrderController::class, 'sendOrderEvent']);

Route::get('/login-admin', [AuthController::class, 'index']);
Route::prefix('admin')->group(function(){
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('header', [HomeController::class, 'headerView']);
    Route::get('footer', [HomeController::class, 'footerView']);
    Route::get('order', [OrderController::class, 'orderView']);
    Route::get('cashier', [OrderController::class, 'cashierView']);
    Route::get('menu', [MenuController::class, 'menuView']);
    Route::get('category', [MenuController::class, 'categoryView']);
    Route::get('option', [MenuController::class, 'optionView']);
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
Route::prefix('order')->group(function () {
    Route::get('/data', [OrderController::class, 'dataOrder']);
    Route::get('/data/info', [OrderController::class, 'dataOrderInfo']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
});