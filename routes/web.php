<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\AdminSidebarMenu;

Route::middleware(['web', AdminSidebarMenu::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/product', [ProductController::class, 'index'])->name('products.index');        // list products
    Route::get('/product/create', [ProductController::class, 'create'])->name('products.create');  // create product form
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');    // edit product
    Route::put('/product/{id}', [ProductController::class, 'update'])->name('products.update');

    Route::put('/product/tset', [ProductController::class, 'update'])->name('users.index');
    Route::put('/product/test', [ProductController::class, 'update'])->name('roles.index');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/data', [CategoryController::class, 'data'])->name('categories.data');
    Route::post('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('categories/destroy', [CategoryController::class, 'destroy'])->name('categories.destroy');
});