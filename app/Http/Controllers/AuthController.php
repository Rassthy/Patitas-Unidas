<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'dni_nie' => ['required', 'string', 'max:15', 'unique:users,dni_nie'],
            'telefono' => ['required', 'string', 'max:20', 'unique:users,telefono'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create([
            'username' => $data['username'],
            'dni_nie' => $data['dni_nie'],
            'telefono' => $data['telefono'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'tipo' => 'usuario',
            'activo' => true,
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Cuenta creada correctamente.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Sesion iniciada correctamente.');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}