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
    return redirect()->route('home', ['tab' => 'principal']);
})->name('dashboard');

// Rutas de autenticación (públicas)
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/check-auth', [AuthController::class, 'checkAuth'])->name('check-auth');

// Public post routes
Route::get('posts/{id}/comments', [PostController::class, 'getComments'])->name('posts.comments.index');
Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rutas de perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

    // Rutas de recursos
    Route::resource('users', UserController::class)->only(['index', 'show', 'update', 'destroy']);
    Route::resource('pets', PetController::class)->except(['create', 'edit']);
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('posts/{id}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('posts/{id}/comments', [PostController::class, 'addComment'])->name('posts.comments.store');
    Route::delete('comments/{id}', [PostController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('comments/{id}/like', [PostController::class, 'toggleCommentLike'])->name('comments.like');
    Route::get('chats', [ChatController::class, 'index'])->name('chats.index');
    Route::post('chats', [ChatController::class, 'store'])->name('chats.store');
    Route::get('chats/{id}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('chats/{id}/messages', [ChatController::class, 'sendMessage'])->name('chats.messages.store');
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'update']);
    Route::resource('reports', ReportController::class)->except(['create', 'edit']);
});
