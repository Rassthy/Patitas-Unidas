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
                'foto_perfil' => null,
            ]);

            // Autenticar al usuario automáticamente
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Cuenta creada correctamente. ¡Bienvenido a PatitasUnidas!');
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['error' => 'Error al crear la cuenta. Por favor, intenta de nuevo.']);
        }
    }

    /**
     * Iniciar sesión con un usuario existente.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'El usuario o correo es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Comprobacion de si el input es un email o un username
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Redirigimos a la ruta donde intentaba ir, o al home por defecto
            return redirect()->intended(route('home'));
        }

        // Si falla, devolvemos a al login marcando el error del campo que falle
        return back()->withErrors([
            'login' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput($request->only('login', 'remember'));
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
