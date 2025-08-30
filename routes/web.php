<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'indexHome']);

// rota para detalhes do produto
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

// auth routes
Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

// permission routes
Route::get('/permission-denied', function () {
    return view('errors.permission-denied');
})->name('permission-denied');

// admin route, com middleware
Route::middleware(['admin'])->group(function () {
    Route::get('/admin', [UserController::class, 'index'])->name('admin.index');

    // produtos
    Route::prefix('admin/products')->name('admin.products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::post('/{id}/deactivate', [ProductController::class, 'deactivate'])->name('deactivate');
        Route::get('/{id}/variations', [ProductController::class, 'showVariations'])->name('variations');
        Route::get('/{id}/stock', [ProductController::class, 'showStockForm'])->name('stock');
        Route::put('/{id}/stock', [ProductController::class, 'updateStock'])->name('updateStock');
        Route::get('/{product}/variations/{variation}/stock', [ProductController::class, 'showVariationStockForm'])->name('variations.stock');
        Route::put('/{product}/variations/{variation}/stock', [ProductController::class, 'updateVariationStock'])->name('variations.updateStock');
    });

    // categorias
    Route::prefix('admin/categories')->name('admin.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });
});