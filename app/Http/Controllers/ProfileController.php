<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
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

        $isOwner = auth()->check() && auth()->id() === $user->id;

        $user->load([
            'pets.images',
            'pets.vaccines',
            'pets.reminders' => function ($q) { $q->orderBy('fecha_alarma'); },
            'ratings' => function ($query) { $query->latest(); },
            'ratings.voter',
            'posts.images',
            'posts.category',
            'donations',
            'ratingsGiven',
        ]);

        return view('profile.show', compact('user', 'isOwner'));
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

        $this->createNotification(
            $user->id,
            'rating',
            __('Nueva valoración'),
            __(':usuario ha valorado tu perfil con :puntuacion estrellas', [
                'usuario'    => Auth::user()->username,
                'puntuacion' => $request->puntuacion,
            ]),
            '/profile/' . $user->username
        );

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
            'settings'                    => 'required|array',
            'settings.tema'               => 'nullable|string|in:claro,oscuro',
            'settings.idioma'             => 'nullable|string|in:es,en',
            'settings.mostrar_apellidos'  => 'nullable',
            'settings.mostrar_fecha'      => 'nullable',
            'settings.mascotas_publicas'  => 'nullable',
        ]);

        $currentSettings = $user->user_settings ?? [];
        $newSettings     = array_merge($currentSettings, $request->settings);

        $user->update(['user_settings' => $newSettings]);

        App::setLocale($newSettings['idioma'] ?? 'es');
        return redirect()->route('profile.settings')->with('success', __('Preferencias actualizadas correctamente.'));
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
            'nombre'           => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'descripcion'      => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'provincia'        => 'nullable|string|max:50',
            'ciudad'           => 'nullable|string|max:100',
            'foto_perfil'      => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'banner'           => 'nullable|mimes:jpeg,png,jpg,gif,webp,avif|max:4096',
        ], [], [
            'fecha_nacimiento' => 'fecha de nacimiento',
            'descripcion'      => 'descripción'
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

        return redirect()->route('profile.show')->with('success', __('Perfil actualizado correctamente.'));
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validator = \Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => __('La contraseña actual es obligatoria.'),
            'password.required'         => __('La nueva contraseña es obligatoria.'),
            'password.min'              => __('La contraseña debe tener al menos 8 caracteres.'),
            'password.confirmed'        => __('Las contraseñas no coinciden.'),
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.settings')
                ->withErrors($validator)->withInput()->with('tab', 'st-cuenta');
        }

        if (!\Hash::check($request->current_password, $user->password_hash)) {
            return redirect()->route('profile.settings')
                ->withErrors(['current_password' => __('La contraseña actual no es correcta.')])
                ->withInput()->with('tab', 'st-cuenta');
        }

        $user->update(['password_hash' => \Hash::make($request->password)]);

        return redirect()->route('profile.settings')
            ->with('success', __('Contraseña cambiada correctamente.'))->with('tab', 'st-cuenta');
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'confirm_password' => 'required|string',
        ], [
            'confirm_password.required' => __('Debes introducir tu contraseña para confirmar.'),
        ]);

        if (!\Hash::check($request->confirm_password, $user->password_hash)) {
            return redirect()->route('profile.settings')
                ->withInput()
                ->withErrors(['confirm_password' => __('La contraseña no es correcta.')])
                ->with('tab', 'st-cuenta');
        }

        Auth::logout();
        $user->delete();

        return redirect()->route('home')->with('success', __('Tu cuenta ha sido eliminada correctamente.'));
    }

    private function createNotification($userId, $tipo, $titulo, $mensaje, $enlaceUrl = null)
    {
        if ($userId === Auth::id()) return;

        \App\Models\Notification::create([
            'user_id'    => $userId,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'enlace_url' => $enlaceUrl,
            'leida'      => false,
        ]);
    }
}