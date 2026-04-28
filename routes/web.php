<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Ruta pública principal
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/dashboard', function () {
    return redirect()->route('profile.show');
})->name('dashboard');

// Rutas de autenticación (públicas)
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/check-auth', [AuthController::class, 'checkAuth'])->name('check-auth');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('pets', PetController::class)->except(['create', 'edit']);
    Route::resource('posts', PostController::class)->except(['create', 'edit']);
    Route::resource('chats', ChatController::class)->except(['create', 'edit']);
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'update']);
    Route::resource('reports', ReportController::class)->except(['create', 'edit']);
});
