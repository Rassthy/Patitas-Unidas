<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\PetVaccineController;
use App\Http\Controllers\PetReminderController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
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

// Contenido público (Lectura)
Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('posts/{id}/comments', [PostController::class, 'getComments'])->name('posts.comments.index');


/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (Requieren inicio de sesión)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', function () {
        return redirect()->route('home', ['tab' => 'principal']);
    })->name('dashboard');

    // Gestión de Perfil (Rutas específicas)
    // IMPORTANTE: Van antes que la ruta dinámica para evitar el Error 404
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::put('/profile/settings', [ProfileController::class, 'updateSettings'])->name('profile.settings.update');

    // Recursos de Usuario y Mascotas
    Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);
    Route::resource('mis-mascotas', PetController::class)->parameters(['mis-mascotas' => 'pet']);
    Route::post('mis-mascotas/{pet}/vacunas', [PetVaccineController::class, 'store'])->name('pets.vaccines.store');
    Route::delete('mis-mascotas/{pet}/vacunas/{vaccine}', [PetVaccineController::class, 'destroy'])->name('pets.vaccines.destroy');
    Route::post('mis-mascotas/{pet}/recordatorios', [PetReminderController::class, 'store'])->name('pets.reminders.store');
    Route::delete('mis-mascotas/{pet}/recordatorios/{reminder}', [PetReminderController::class, 'destroy'])->name('pets.reminders.destroy');

    // Publicaciones e Interacciones
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

/*
|--------------------------------------------------------------------------
| RUTAS DINÁMICAS (Al final para evitar colisiones)
|--------------------------------------------------------------------------
*/
// Visualización de Perfiles (Pública para SEO, pero al final del archivo)
Route::get('/profile/{identifier?}', [ProfileController::class, 'show'])->name('profile.show');
