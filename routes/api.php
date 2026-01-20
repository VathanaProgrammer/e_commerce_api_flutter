<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\ABASandboxController;
use App\Http\Controllers\Api\MockQRController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\UserOrdersController;

Route::get('sanctum/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});

Route::get('/home', [HomeController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::post('/sale/proccess', [SalesController::class, 'test']);

Route::post('/create-qr', [MockQRController::class, 'createQR']);
Route::get('/test-qr/pay/{tranId}', [MockQRController::class, 'scan']);
Route::get('/auto_pay/{tranId}', [MockQRController::class, 'autoPayAfter2Sec']);
// In api.php
Route::post('/checkout/cash', [MockQRController::class, 'cashCheckout']);
// In routes/api.php
Route::get('/user/orders', [UserOrdersController::class, 'index']);
Route::get('/user/orders/{id}', [UserOrdersController::class, 'show']);

Route::get('/favorites', [FavoriteController::class, 'index']);
Route::post('/favorites/add', [FavoriteController::class, 'add']);
Route::post('/favorites/remove', [FavoriteController::class, 'remove']);