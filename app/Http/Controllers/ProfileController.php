<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Muestra el perfil del usuario
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    // Muestra el formulario de edición del perfil
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Actualiza el perfil del usuario
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'descripcion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:100',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $data = $request->only(['descripcion', 'fecha_nacimiento', 'provincia', 'ciudad']);

        // Manejar foto de perfil
        if ($request->hasFile('foto_perfil')) {
            // Eliminar foto anterior si existe
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $data['foto_perfil'] = $request->file('foto_perfil')->store('profiles', 'public');
        }

        // Manejar banner
        if ($request->hasFile('banner')) {
            // Eliminar banner anterior si existe
            if ($user->banner && Storage::disk('public')->exists($user->banner)) {
                Storage::disk('public')->delete($user->banner);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }
}