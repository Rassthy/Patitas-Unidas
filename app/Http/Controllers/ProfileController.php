<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function show($identifier = null)
    {
        if (!$identifier) {
            if (auth()->check()) {
                $user = auth()->user();
            } else {
                return redirect()->route('login');
            }
        } else {
            $user = User::where('username', $identifier)->firstOrFail();
        }

        // Optimizamos la consulta para cargar solo lo necesario y evitar el N+1, aparte tambien ordenamos las valoracioes
        $user->load([
            'pets',
            'ratings' => function($query) {
                $query->latest();
            },
            'ratings.voter',
            'posts.images',
            'posts.category'
        ]);

        return view('profile.show', compact('user'));
    }

    public function storeRating(Request $request, $id)
    {
        $request->validate([
            'puntuacion' => 'required|numeric|min:0.5|max:5',
            'comentario' => 'nullable|string|max:500',
        ], [], [
            'puntuacion' => 'puntuación',
            'comentario' => 'comentario'
        ]);

        if (Auth::id() == $id) {
            return back()->with('error', 'No puedes valorarte a ti mismo.');
        }

        $user = User::findOrFail($id);

        $user->ratings()->where('voter_id', Auth::id())->delete();

        $user->ratings()->create([
            'voter_id'   => Auth::id(),
            'puntuacion' => $request->puntuacion,
            'comentario' => $request->comentario,
        ]);

        return back()->with('success', '¡Valoración publicada correctamente!');
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

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

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
        ], [], [
            'fecha_nacimiento' => 'fecha de nacimiento',
            'descripcion' => 'descripción'
        ]);

        $data = $request->only(['nombre', 'apellidos', 'descripcion', 'fecha_nacimiento', 'provincia', 'ciudad']);

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil && Storage::disk('public')->exists($user->foto_perfil)) {
                Storage::disk('public')->delete($user->foto_perfil);
            }
            $data['foto_perfil'] = $request->file('foto_perfil')->store('profiles', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($user->banner && Storage::disk('public')->exists($user->banner)) {
                Storage::disk('public')->delete($user->banner);
            }
            $data['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $user->update($data);

        return redirect()->route('profile.show', $user->username)->with('success', 'Perfil actualizado correctamente.');
    }
}