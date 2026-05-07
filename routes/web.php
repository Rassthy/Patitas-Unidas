<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetReminderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DonationController;

/*
*    RUTAS PROTEGIDAS (Requieren inicio de sesión)
*/

// Home | Landing
Route::get('/', function () {
    return view('home');
})->name('home');

// Autenticación
Route::get('/login', function () {
    return redirect()->route('home')->with('error', 'Debes iniciar sesión primero.');
})->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::get('/check-auth', [AuthController::class, 'checkAuth'])->name('check-auth');

// Restablecimiento de Contraseña
Route::get('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');

// Contenido público (Lectura)
Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('posts/{id}/comments', [PostController::class, 'getComments'])->name('posts.comments.index');

// Pets públicos (info básica, respeta privacidad del dueño)
Route::get('pets/{id}/public', [PetController::class, 'show'])->name('pets.public.show');

/* 
*    RUTAS PROTEGIDAS (Requieren inicio de sesión)
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return redirect()->route('home', ['tab' => 'principal']);
    })->name('dashboard');

    // Gestión de Perfil
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::delete('/profile/account', [ProfileController::class, 'deleteAccount'])->name('profile.account.destroy');

    // Recursos de Usuario
    Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);
    Route::get('users/search', [UserController::class, 'search'])->name('users.search');

    // Mascotas (CRUD completo con imágenes y vacunas)
    Route::get('pets', [PetController::class, 'index'])->name('pets.index');
    Route::post('pets', [PetController::class, 'store'])->name('pets.store');
    Route::get('pets/{id}', [PetController::class, 'show'])->name('pets.show');
    Route::post('pets/{id}', [PetController::class, 'update'])->name('pets.update'); // POST para FormData con ficheros
    Route::delete('pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');

    // Recordatorios de mascotas
    Route::post('pets/{petId}/reminders', [PetReminderController::class, 'store'])->name('pets.reminders.store');
    Route::delete('pets/{petId}/reminders/{reminderId}', [PetReminderController::class, 'destroy'])->name('pets.reminders.destroy');

    // Publicaciones e Interacciones
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('posts/{id}/like', [PostController::class, 'toggleLike'])->name('posts.like');
    Route::post('posts/{id}/comments', [PostController::class, 'addComment'])->name('posts.comments.store');
    Route::post('/profile/{id}/rate', [ProfileController::class, 'storeRating'])->name('profile.rate');
    Route::delete('comments/{id}', [PostController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('comments/{id}/like', [PostController::class, 'toggleCommentLike'])->name('comments.like');

    // Chat
    Route::get('chats', [ChatController::class, 'index'])->name('chats.index');
    Route::post('chats', [ChatController::class, 'store'])->name('chats.store');
    Route::get('chats/{id}', [ChatController::class, 'show'])->name('chats.show');
    Route::post('chats/{id}/messages', [ChatController::class, 'sendMessage'])->name('chats.messages.store');

    // Comprobador silencioso de nuevas notificaciones (¡Debe ir ANTES del resource!)
    Route::get('/notifications/check', function () {
        $latest = \App\Models\Notification::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('leida', false)
            ->latest()
            ->first();
            
        return response()->json([
            'latest' => $latest ? $latest->only(['id', 'titulo', 'mensaje']) : null
        ]);
    })->name('notifications.check');

    // Notificaciones y Reportes
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'update']);
    Route::resource('reports', ReportController::class)->except(['create', 'edit']);

    // Donaciones
    Route::get('/donar', [DonationController::class, 'index'])->name('donate');
    Route::post('/donations/create-order', [DonationController::class, 'createOrder']);
    Route::post('/donations/capture-order', [DonationController::class, 'captureOrder']);
});

/* 
*    RUTAS DINÁMICAS (Al final para evitar colisiones)
*/
Route::get('/profile/{identifier?}', [ProfileController::class, 'show'])->name('profile.show');