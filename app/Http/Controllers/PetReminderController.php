<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetReminder;
use Carbon\Carbon;
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
            'fecha_alarma' => 'required|date',
            'timezone'     => 'nullable|string'
        ], [
            'titulo.required'       => 'El título es obligatorio.',
            'fecha_alarma.required' => 'La fecha es obligatoria.',
        ]);

        $tz = $data['timezone'] ?? 'Europe/Madrid';

        $fechaUtc = Carbon::parse($data['fecha_alarma'], $tz)->setTimezone('UTC');

        if ($fechaUtc->isPast()) {
            return response()->json(['message' => 'La fecha debe ser futura.'], 422);
        }

        $reminder = PetReminder::create([
            'pet_id'       => $pet->id,
            'titulo'       => $data['titulo'],
            'mensaje'      => $data['mensaje'] ?? null,
            'fecha_alarma' => $fechaUtc,
            'timezone'     => $tz,
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