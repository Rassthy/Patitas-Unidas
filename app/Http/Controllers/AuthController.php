<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Registrar un nuevo usuario.
     */
    public function register(RegisterRequest $request)
    {
        // El FormRequest ya ha validado los datos
        $data = $request->validated();

        try {
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
                'foto_perfil' => 'defaults/foto_perfil_generica.png',
            ]);

            // Autenticar al usuario automáticamente
            Auth::login($user);

            return redirect('/')->with('success', 'Cuenta creada correctamente. ¡Bienvenido a PatitasUnidas!');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['error' => 'Error al crear la cuenta. Por favor, intenta de nuevo.']);
        }
    }

    /**
     * Iniciar sesión con un usuario existente.
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Intentar autenticación con email y contraseña
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'Sesión iniciada correctamente.');
        }

        // Si falla, devolver error específico
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'login' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ], 'login');
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }

    /**
     * Verificar si el usuario está autenticado (para AJAX).
     */
    public function checkAuth()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user' => Auth::user() ? Auth::user()->only(['id', 'username', 'email', 'nombre', 'apellidos']) : null,
        ]);
    }
}
