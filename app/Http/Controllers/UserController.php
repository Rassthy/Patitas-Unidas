<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'username', 'email', 'nombre', 'apellidos', 'tipo', 'activo')->get();
        return response()->json(['users' => $users], 200);
    }

    public function show(User $user)
    {
        return response()->json(['user' => $user], 200);
    }

    public function update(Request $request, User $user)
    {
        abort_if(Auth::id() !== $user->id, 403, 'No autorizado.');

        $data = $request->validate([
            'username' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'nombre' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'telefono' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)],
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $user->update($data);
        return response()->json(['user' => $user], 200);
    }

    public function destroy(User $user)
    {
        abort_if(Auth::id() !== $user->id, 403, 'No autorizado.');

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente.'], 200);
    }
}
