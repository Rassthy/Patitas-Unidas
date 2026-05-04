<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;          // Controlador del CRUD de mascotas
use App\Http\Controllers\PetVaccineController;   // Controlador de vacunas
use App\Http\Controllers\PetReminderController;  // Controlador de recordatorios
use Illuminate\Support\Facades\Route;

// Página de inicio — pública para todo el mundo
Route::get('/', function () {
    return view('home');
});

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Área privada — el middleware 'auth' bloquea esta sección si el usuario no está logueado
Route::middleware('auth')->group(function () {

    // Route::resource crea automáticamente las 7 rutas del CRUD:
    // GET /mis-mascotas          → index   (listar)
    // GET /mis-mascotas/create   → create  (formulario nuevo)
    // POST /mis-mascotas         → store   (guardar nuevo)
    // GET /mis-mascotas/{pet}    → show    (ver detalle)
    // GET /mis-mascotas/{pet}/edit → edit  (formulario edición)
    // PUT /mis-mascotas/{pet}    → update  (guardar cambios)
    // DELETE /mis-mascotas/{pet} → destroy (borrar)
    Route::resource('mis-mascotas', PetController::class)->parameters(['mis-mascotas' => 'pet']);

    // Ruta para añadir una vacuna a una mascota concreta
    Route::post('mis-mascotas/{pet}/vacunas', [PetVaccineController::class, 'store'])->name('pets.vaccines.store');
    // Ruta para borrar una vacuna concreta de una mascota concreta
    Route::delete('mis-mascotas/{pet}/vacunas/{vaccine}', [PetVaccineController::class, 'destroy'])->name('pets.vaccines.destroy');

    // Ruta para añadir un recordatorio
    Route::post('mis-mascotas/{pet}/recordatorios', [PetReminderController::class, 'store'])->name('pets.reminders.store');
    // Ruta para borrar un recordatorio
    Route::delete('mis-mascotas/{pet}/recordatorios/{reminder}', [PetReminderController::class, 'destroy'])->name('pets.reminders.destroy');
});
