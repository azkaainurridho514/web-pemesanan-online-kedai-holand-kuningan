<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OptionController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;

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

Route::prefix('get-data')->group(function () {
    Route::get('product', [ProductController::class, 'getData']);
    Route::get('product-dashboard', [ProductController::class, 'getDataDashboard']);
    Route::get('options', [OptionController::class, 'getDataOption']);
    Route::get('options/{id}', [OptionController::class, 'getDataOptionDetail']);
    Route::get('product/{id}', [ProductController::class, 'show']);
    Route::get('category', [CategoryController::class, "getData"]);
    Route::get('category/{id}', [CategoryController::class, 'show']);
    Route::get('user', [UserController::class, "getData"]);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::get('form-menu', [ProductController::class, 'masterFormData']);
});
Route::prefix('update-data')->group(function () {
    Route::put('product/{id}', [ProductController::class, 'update']);
    Route::put('category/{id}', [CategoryController::class, 'update']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::put('option/{id}', [OptionController::class, 'update']);
    Route::put('order/{id}', [OrderController::class, 'update']);
});
Route::prefix('create-data')->group(function () {
    Route::post('product', [ProductController::class, 'store']);
    Route::post('category', [CategoryController::class, 'store']);
    Route::post('user', [UserController::class, 'store']);
    Route::post('option', [OptionController::class, 'store']);
    Route::post('order', [OrderController::class, 'store']);
});
Route::prefix('delete-data')->group(function () {
    Route::delete('product/{id}', [ProductController::class, 'destroy']);
    Route::delete('category/{id}', [CategoryController::class, 'destroy']);
    Route::delete('option/{id}', [OptionController::class, 'destroy']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
});


