<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
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
Route::get('/order', function(){
    broadcast(new OrderEvent('info',"ADA PESANAN","Pesanan masuk dari mamank garox"));
});
Route::prefix('get-data')->group(function () {
    Route::get('category', [CategoryController::class, "getData"]);
    Route::get('product', [ProductController::class, 'getData']);
});
