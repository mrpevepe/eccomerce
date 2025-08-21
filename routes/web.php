<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// rotas de auth
Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/auth/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('logout');

// rotas admin e users
Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('admin');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// permissão negada
Route::get('/permission-denied', function () {
    return view('errors.permission-denied');
})->name('permission-denied');
