<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    // Determinamos si el usuario está autorizado
    public function authorize(): bool
    {
        return true;
    }

    // Obtenemos las reglas de validación para el inicio de sesión
    public function rules(): array
    {
        $emailValidation = app()->environment('testing') 
            ? 'required|email|max:150'
            : 'required|email:rfc,dns|max:150';

        return [
            'email' => $emailValidation,
            'password' => [
                'required',
                'string',
                'min:8',
            ],
        ];
    }

    // Obtenemos los mensajes personalizados para los errores
    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no puede exceder 150 caracteres.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
