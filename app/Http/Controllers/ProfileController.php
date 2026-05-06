<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    // Muestra el perfil del usuario
    public function show($identifier = null)
    {
        if (!$identifier) {
            $user = Auth::user();
        } else {
            $cleanIdentifier = ltrim($identifier, '@');

            if (is_numeric($cleanIdentifier)) {
                $user = User::findOrFail($cleanIdentifier);
            } else {
                $user = User::where('username', $cleanIdentifier)->firstOrFail();
            }
        }

        $user->load('pets');

        return view('profile.show', compact('user'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'settings' => 'required|array',
            'settings.tema' => 'nullable|string|in:claro,oscuro',
            'settings.idioma' => 'nullable|string|in:es,en',
        ]);

        $user->update([
            'user_settings' => $request->settings
        ]);

        return redirect()->route('profile.settings')->with('success', 'Preferencias actualizadas correctamente.');
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
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:100',
            'foto_perfil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $data = $request->only(['nombre', 'apellidos', 'descripcion', 'fecha_nacimiento', 'provincia', 'ciudad']);

        // Manejar foto de perfil
        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $data['foto_perfil'] = $request->file('foto_perfil')->store('profiles', 'public');
        }

        // Manejar banner
        if ($request->hasFile('banner')) {
            if ($user->banner && Storage::disk('public')->exists($user->banner)) {
                Storage::disk('public')->delete($user->banner);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show')->with('success', 'Perfil actualizado correctamente.');
    }
}