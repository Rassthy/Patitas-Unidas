<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index()
    {
        $pets = Pet::where('user_id', Auth::id())->get();
        return response()->json(['pets' => $pets], 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'especie' => 'nullable|string|max:50',
            'raza' => 'nullable|string|max:50',
            'edad' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'foto' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();
        $pet = Pet::create($data);

        return response()->json(['pet' => $pet], 201);
    }

    public function show($id)
    {
        $pet = Pet::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['pet' => $pet], 200);
    }

    public function update(Request $request, $id)
    {
        $pet = Pet::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'especie' => 'nullable|string|max:50',
            'raza' => 'nullable|string|max:50',
            'edad' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'foto' => 'nullable|string|max:255',
        ]);

        $pet->update($data);
        return response()->json(['pet' => $pet], 200);
    }

    public function destroy($id)
    {
        $pet = Pet::where('user_id', Auth::id())->findOrFail($id);
        $pet->delete();
        return response()->json(['message' => 'Pet deleted successfully.'], 200);
    }
}
