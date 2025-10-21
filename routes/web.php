<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
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
Route::get('/order', function(){
    broadcast(new OrderEvent('info',"ADA PESANAN","Pesanan masuk dari mamank garox"));
});

Route::get('/login-admin', [AuthController::class, 'index']);
Route::prefix('admin')->group(function(){
    Route::get('dashboard', [DashboardController::class, 'index']);
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'view']);
    Route::get('/data', [CartController::class, 'getData']);
    Route::get('/history', [CartController::class, 'getHistory']);
    Route::post('/add', [CartController::class, 'addOrUpdate']);
    Route::delete('/remove/{menu_id}', [CartController::class, 'remove']);
    Route::delete('/clear', [CartController::class, 'clear']);
});