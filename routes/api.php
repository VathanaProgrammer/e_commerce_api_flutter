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
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ComparisonController;
use App\Http\Controllers\Api\AnalyticsController;

Route::get('sanctum/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);

// User Profile Routes
Route::get('/user/profile/{id}', [UserProfileController::class, 'show']);
Route::post('/user/profile/{id}', [UserProfileController::class, 'update']);
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

// Cart Routes (Public - supports both auth and guest)
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'add']);
    Route::put('/{id}', [CartController::class, 'update']);
    Route::delete('/{id}', [CartController::class, 'remove']);
    Route::delete('/', [CartController::class, 'clear']);
});

// Search & Filter Routes
Route::prefix('search')->group(function () {
    Route::get('/', [SearchController::class, 'search']);
    Route::get('/suggestions', [SearchController::class, 'suggestions']);
    Route::get('/filters', [SearchController::class, 'filters']);
});

// Coupon Routes
Route::prefix('coupons')->group(function () {
    Route::get('/', [CouponController::class, 'index']);
    Route::post('/validate', [CouponController::class, 'validate']);
});

// Product Comparison Routes (Public - supports both auth and guest)
Route::prefix('comparison')->group(function () {
    Route::get('/', [ComparisonController::class, 'index']);
    Route::post('/add', [ComparisonController::class, 'add']);
    Route::delete('/{productId}', [ComparisonController::class, 'remove']);
    Route::delete('/', [ComparisonController::class, 'clear']);
});

// Newsletter Routes
Route::prefix('newsletter')->group(function () {
    Route::post('/subscribe', [NewsletterController::class, 'subscribe']);
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe']);
    Route::get('/verify/{token}', [NewsletterController::class, 'verify']);
});

// Protected API routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Address Management
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::put('/{id}', [AddressController::class, 'update']);
        Route::delete('/{id}', [AddressController::class, 'destroy']);
        Route::post('/{id}/set-default', [AddressController::class, 'setDefault']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
    });

    // Analytics (Admin only - add middleware as needed)
    Route::prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('/sales-report', [AnalyticsController::class, 'salesReport']);
        Route::get('/product/{productId}/performance', [AnalyticsController::class, 'productPerformance']);
    });

    // Review API Routes
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::get('/{review}', [ReviewController::class, 'show']);
        Route::put('/{review}', [ReviewController::class, 'update']);
        Route::delete('/{review}', [ReviewController::class, 'destroy']);
        Route::post('/{review}/vote-helpful', [ReviewController::class, 'voteHelpful']);
        Route::get('/criteria', [ReviewController::class, 'criteria']);
        Route::get('/user', [ReviewController::class, 'userReviews']);
    });

    // Product Reviews (public but can be enhanced with auth for additional features)
    Route::get('/products/{product}/reviews', [ReviewController::class, 'productReviews']);
});