<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\AdminSidebarMenu;
use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;

/*
|--------------------------------------------------------------------------
| Guest routes (NOT logged in)
|--------------------------------------------------------------------------
|*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/check-business', function () {
        dd(session('business'));
    });

    Route::get('/', [HomeController::class, 'index'])->name('home');
    
    // Notification Polling (Accessible by both Admin and Staff)
    Route::get('/notifications/check', [\App\Http\Controllers\NotificationController::class, 'checkNewOrders'])->name('notifications.check');

    // Admin & Staff Routes
    Route::middleware(['role:admin|staff'])->group(function () {
        Route::get('/product', [ProductController::class, 'index'])->name('products.index');
        Route::get('/product/data', [ProductController::class, 'data'])->name('products.data');
        Route::get('/product/list', [ProductController::class, 'list'])->name('products.list');
        Route::get('/product/create', [ProductController::class, 'create'])->name('products.create');
        Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/product/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/product', [ProductController::class, 'store'])->name('products.store');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/data', [CategoryController::class, 'data'])->name('categories.data');
        Route::post('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::prefix('attributes')->group(function () {
            Route::get('/', [AttributeController::class, 'index'])->name('attributes.index');
            Route::get('/data', [AttributeController::class, 'data'])->name('attributes.data');
            Route::get('/create', [AttributeController::class, 'create'])->name('attributes.create');
            Route::post('/store', [AttributeController::class, 'store'])->name('attributes.store');
            Route::get('/{id}/edit', [AttributeController::class, 'edit'])->name('attributes.edit');
            Route::put('/{id}', [AttributeController::class, 'update'])->name('attributes.update');
            Route::delete('/{id}', [AttributeController::class, 'destroy'])->name('attributes.destroy');
        });

        Route::prefix('sales')->group(function () {
            Route::get('/orders', [App\Http\Controllers\SalesController::class, 'index'])->name('sales.orders');
            Route::get('/orders/data', [App\Http\Controllers\SalesController::class, 'data'])->name('sales.orders.data');
            Route::get('/{id}', [App\Http\Controllers\SalesController::class, 'show'])->name('sales.show');
            Route::get('/{id}/edit', [App\Http\Controllers\SalesController::class, 'edit'])->name('sales.edit');
            Route::get('/{id}/print', [App\Http\Controllers\SalesController::class, 'printInvoice'])->name('sales.print');
            Route::delete('/{id}', [App\Http\Controllers\SalesController::class, 'destroy'])->name('sales.destroy');
        });

        // Review Management Routes (Admin & Staff)
        Route::prefix('reviews')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('reviews.index');
            Route::get('/datatable', [ReviewController::class, 'getDataTable'])->name('reviews.datatable');
            Route::get('/create', [ReviewController::class, 'create'])->name('reviews.create');
            Route::post('/', [ReviewController::class, 'store'])->name('reviews.store');
            Route::get('/{review}', [ReviewController::class, 'show'])->name('reviews.show');
            Route::get('/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
            Route::put('/{review}', [ReviewController::class, 'update'])->name('reviews.update');
            Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
            Route::post('/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
            Route::post('/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
            Route::post('/{review}/feature', [ReviewController::class, 'feature'])->name('reviews.feature');
            Route::post('/{review}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');
            Route::post('/{review}/vote-helpful', [ReviewController::class, 'voteHelpful'])->name('reviews.vote-helpful');
            Route::get('/product/{product}', [ReviewController::class, 'getProductReviews'])->name('reviews.product');
        });
    });

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])->name('users.index');
            Route::get('/data', [\App\Http\Controllers\UserController::class, 'data'])->name('users.data');
            Route::get('/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
            Route::post('/store', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
            Route::get('/{id}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
            Route::put('/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
            Route::get('/profile/{id}', [\App\Http\Controllers\UserController::class, 'profile'])->name('users.profile');
            // Route::get('/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
            Route::delete('/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
            Route::get('/data', [\App\Http\Controllers\RoleController::class, 'data'])->name('roles.data');
            Route::get('/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
            Route::post('/store', [\App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
            Route::get('/{id}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
            Route::put('/{id}', [\App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
            Route::delete('/{id}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
        });

        Route::prefix('business')->group(function () {
            Route::get('/settings', [BusinessController::class, 'index'])->name('business.settings');
            Route::get('/data', [BusinessController::class, 'data'])->name('business.data');
            Route::get('/{id}', [BusinessController::class, 'show'])->name('business.show');
            Route::post('/settings/{id?}', [BusinessController::class, 'update'])->name('business.settings.update');
            Route::delete('/{id}', [BusinessController::class, 'destroy'])->name('business.destroy');
            Route::delete('/logo', [BusinessController::class, 'removeLogo'])->name('business.logo.remove');
        });
    });

});