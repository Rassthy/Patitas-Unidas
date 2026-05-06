<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => __('El correo es obligatorio.'),
            'email.email'    => __('Introduce un correo válido.'),
            'email.exists'   => __('No encontramos ninguna cuenta con ese correo.'),
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __('Te hemos enviado el enlace de recuperación. Revisa tu bandeja de entrada.'))
            : back()->withErrors(['email' => __('No pudimos enviar el enlace. Inténtalo de nuevo.')]);
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required'  => __('La contraseña es obligatoria.'),
            'password.min'       => __('La contraseña debe tener al menos 8 caracteres.'),
            'password.confirmed' => __('Las contraseñas no coinciden.'),
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password_hash' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('home')->with('success', __('Contraseña restablecida correctamente. Ya puedes iniciar sesión.'))
            : back()->withErrors(['email' => __($status)]);
    }
}