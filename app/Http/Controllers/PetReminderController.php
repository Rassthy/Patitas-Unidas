<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controlador exclusivo para los recordatorios
class PetReminderController extends Controller
{
    // STORE — crea un recordatorio nuevo para una mascota
    public function store(Request $request, Pet $pet)
    {
        // Solo el dueño puede añadir recordatorios a su mascota
        abort_if($pet->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'titulo'       => ['required', 'string', 'max:100'],
            'mensaje'      => ['nullable', 'string'],
            'fecha_alarma' => ['required', 'date'], // Fecha y hora del aviso
        ]);

        $data['pet_id'] = $pet->id; // Lo asociamos a esta mascota
        PetReminder::create($data);

        return redirect()->route('mis-mascotas.show', $pet)->with('success', 'Recordatorio creado.');
    }

    // DESTROY — borra un recordatorio
    public function destroy(Pet $pet, PetReminder $reminder)
    {
        abort_if($pet->user_id !== Auth::id(), 403);      // Dueño de la mascota
        abort_if($reminder->pet_id !== $pet->id, 403);   // El recordatorio pertenece a esa mascota

        $reminder->delete();

        return redirect()->route('mis-mascotas.show', $pet)->with('success', 'Recordatorio eliminado.');
    }
}
