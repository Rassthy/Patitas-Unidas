<?php

namespace App\Http\Controllers;

use App\Mail\VerificacionEmailMail;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // REGISTRO
    public function register(RegisterRequest $request)
    {
        $data    = $request->validated();
        $isOrg   = $data['tipo'] === 'organizacion';

        try {
            $userData = [
                'username'      => $data['username'],
                'email'         => $data['email'],
                'telefono'      => $data['telefono'],
                'password_hash' => Hash::make($data['password']),
                'activo'        => false,          // inactivo hasta verificar
                'email_verificado' => false,
                'foto_perfil'   => null,
            ];

            if ($isOrg) {
                $userData['tipo']                = 'organizacion';
                $userData['nombre']              = $data['nombre_organizacion'];
                $userData['apellidos']           = '';
                $userData['nombre_organizacion'] = $data['nombre_organizacion'];
                $userData['tipo_organizacion']   = $data['tipo_organizacion'];
                $userData['cif']                 = $data['cif'];
                $userData['direccion']           = $data['direccion'] ?? null;
                $userData['web']                 = $data['web'] ?? null;
                $userData['provincia']           = $data['provincia'] ?? null;
                $userData['ciudad']              = $data['ciudad'] ?? null;
            } else {
                $userData['tipo']      = 'usuario';
                $userData['nombre']    = $data['nombre'];
                $userData['apellidos'] = $data['apellidos'];
                $userData['dni_nie']   = $data['dni_nie'];
            }

            // ¡CORRECCIÓN VITAL! Le decimos a la BD que los usuarios normales sí están aprobados
            $userData['is_approved'] = ($userData['tipo'] === 'usuario');

            $user = User::create($userData);

            // Generar y cachear el código 10 minutos
            $codigo = strval(random_int(100000, 999999));
            Cache::put("email_verify_{$user->id}", $codigo, now()->addMinutes(10));

            // Enviar el email
            Mail::to($user->email)->send(new VerificacionEmailMail($codigo, $user->nombre, $user->id));

            return redirect()->route('home')->with('register_success', '¡Registro exitoso! Por favor, verifica tu correo antes de iniciar sesión.');

        } catch (\Exception $e) {
            Log::error('Error en registro: ' . $e->getMessage());
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Error al crear la cuenta. Por favor, intenta de nuevo.']);
        }
    }

    // MOSTRAR FORMULARIO DE VERIFICACIÓN
    public function mostrarVerificacionForm()
    {
        if (!session('verificacion_user_id')) {
            return redirect()->route('home');
        }
        return view('auth.verificar-email');
    }

    // VERIFICAR CÓDIGO (MANUAL)
    public function verificarEmail(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|size:6',
        ], [
            'codigo.required' => 'Debe introducir el código que le enviamos a su correo.',
            'codigo.size'     => 'El código debe tener exactamente 6 dígitos.',
        ]);

        $userId = session('verificacion_user_id');

        if (!$userId) {
            return redirect()->route('home')
                ->withErrors(['error' => 'Sesión de verificación expirada. Regístrate de nuevo.']);
        }

        $user = User::findOrFail($userId);
        $codigoGuardado = Cache::get("email_verify_{$userId}");

        if (!$codigoGuardado || $request->codigo !== $codigoGuardado) {
            return back()->withErrors(['codigo' => 'Código incorrecto o expirado.']);
        }

        $user->update([
            'email_verificado' => true,
            'activo'           => true,
        ]);

        Cache::forget("email_verify_{$userId}");
        session()->forget('verificacion_user_id');

        if (!$user->is_approved) {
            return redirect()->route('home')
                ->with('success', '¡Correo verificado! Ahora el Staff revisará tu documentación para activarte. 🐾');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', '¡Bienvenido a PatitasUnidas! 🐾 Tu correo ha sido verificado.');
    }

    // REENVIAR CÓDIGO
    public function reenviarCodigo(Request $request)
    {
        $userId = session('verificacion_user_id');
        if (!$userId) return redirect()->route('home');

        $user   = User::findOrFail($userId);
        $codigo = strval(random_int(100000, 999999));

        Cache::put("email_verify_{$userId}", $codigo, now()->addMinutes(10));
        Mail::to($user->email)->send(new VerificacionEmailMail($codigo, $user->nombre, $user->id));

        return back()->with('success', 'Te hemos reenviado el código a ' . $user->email);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $request->login,
            'password' => $request->password,
        ];

        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user(); 
            
            // 1. USUARIO NO HA VERIFICADO EMAIL
            if (!$user->email_verificado) {
                session(['verificacion_user_id' => $user->id]);
                session(['verificacion_email'   => $user->email]);
                Auth::logout(); 

                return redirect()->route('verificar.email.form')
                    ->with('info', 'Redirigiendo a verificación de correo...');
            }

            // 2. PROTECTORA/EMPRESA NO APROBADA POR STAFF
            if (!$user->is_approved) {
                Auth::logout();
                // ¡CORRECCIÓN! Usamos withErrors para forzar al Modal a quedarse abierto y mostrar el aviso.
                return back()->withErrors(['error' => 'Tu cuenta está pendiente de validación por el Staff.']);
            }

            // 3. PASÓ TODOS LOS FILTROS
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'login' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput($request->only('login', 'remember'));
    }

    // Verificar CÓDIGO automáticamente (1 Clic)
    public function verificarEmailAuto($id, $codigo)
    {
        $codigoGuardado = Cache::get("email_verify_{$id}");

        if (!$codigoGuardado || $codigo !== $codigoGuardado) {
            return redirect()->route('home')->withErrors(['error' => 'El enlace de verificación es inválido o ha expirado.']);
        }

        $user = User::findOrFail($id);

        $user->update([
            'email_verificado' => true,
            'activo'           => true,
        ]);

        Cache::forget("email_verify_{$id}");
        session()->forget(['verificacion_user_id', 'verificacion_email']);

        if (!$user->is_approved) {
            return redirect()->route('home')
                ->with('success', '¡Correo verificado! Ahora el Staff revisará tu documentación para activarte. 🐾');
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->route('home')->with('success', '¡Bienvenido a PatitasUnidas! Correo verificado y sesión iniciada. 🐾');
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Sesión cerrada correctamente.');
    }

    public function checkAuth()
    {
        return response()->json([
            'authenticated' => Auth::check(),
            'user'          => Auth::user() ? Auth::user()->only(['id', 'username', 'email', 'nombre', 'apellidos']) : null,
        ]);
    }
}