<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetReminderController extends Controller
{
    public function store(Request $request, $petId)
    {
        $pet = Pet::where('user_id', Auth::id())->findOrFail($petId);

        $data = $request->validate([
            'titulo'       => 'required|string|max:100',
            'mensaje'      => 'nullable|string',
            'fecha_alarma' => 'required|date|after:now',
        ], [
            'titulo.required'       => 'El título es obligatorio.',
            'fecha_alarma.required' => 'La fecha es obligatoria.',
            'fecha_alarma.after'    => 'La fecha debe ser futura.',
        ]);

        $reminder = PetReminder::create([
            'pet_id'       => $pet->id,
            'titulo'       => $data['titulo'],
            'mensaje'      => $data['mensaje'] ?? null,
            'fecha_alarma' => $data['fecha_alarma'],
            'notificado'   => false,
        ]);

        return response()->json(['reminder' => $reminder], 201);
    }

    public function destroy($petId, $reminderId)
    {
        $pet      = Pet::where('user_id', Auth::id())->findOrFail($petId);
        $reminder = PetReminder::where('pet_id', $pet->id)->findOrFail($reminderId);
        $reminder->delete();

        return response()->json(['message' => 'Recordatorio eliminado.'], 200);
    }
}