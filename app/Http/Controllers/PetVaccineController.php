<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetVaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controlador exclusivo para las vacunas — solo necesita crear y borrar
class PetVaccineController extends Controller
{
    // STORE — guarda una vacuna nueva asociada a una mascota
    public function store(Request $request, Pet $pet)
    {
        // Seguridad: solo el dueño de la mascota puede añadir vacunas
        abort_if($pet->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'nombre_vacuna'        => ['required', 'string', 'max:100'],
            'fecha_administracion' => ['required', 'date'],
            'proxima_dosis'        => ['nullable', 'date', 'after:fecha_administracion'], // La próxima dosis debe ser DESPUÉS de la actual
        ]);

        $data['pet_id'] = $pet->id; // Vinculamos la vacuna a esta mascota concreta
        PetVaccine::create($data);  // Guardamos en la base de datos

        return redirect()->route('mis-mascotas.show', $pet)->with('success', 'Vacuna registrada.');
    }

    // DESTROY — borra una vacuna
    public function destroy(Pet $pet, PetVaccine $vaccine)
    {
        abort_if($pet->user_id !== Auth::id(), 403);    // El usuario debe ser dueño de la mascota
        abort_if($vaccine->pet_id !== $pet->id, 403);  // La vacuna debe pertenecer a esa mascota (evita manipulación de URLs)

        $vaccine->delete();

        return redirect()->route('mis-mascotas.show', $pet)->with('success', 'Vacuna eliminada.');
    }
}
