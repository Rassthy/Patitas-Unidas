<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $emailValidation = app()->environment('testing') 
            ? 'required|email|max:150|unique:users,email'
            : 'required|email:rfc,dns|max:150|unique:users,email';

        return [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-záéíóúñ\s\-\']+$/i', // Solo letras, espacios, guiones y apóstrofes
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-záéíóúñ\s\-\']+$/i',
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_\-\.]+$/', // Solo alphanumeric, guiones, puntos y guiones bajos
            ],
            'email' => $emailValidation,
            'dni_nie' => [
                'required',
                'string',
                'max:15',
                'unique:users,dni_nie',
                'regex:/^[0-9]{8}[A-Z]$|^[XYZ][0-9]{7}[A-Z]$/', // DNI/NIE/NIF español
            ],
            'telefono' => [
                'required',
                'string',
                'max:20',
                'unique:users,telefono',
                'regex:/^(\+34|0034|34)?[\s\-]?(6|7|9)[\s\-]?([0-9]{8})$|^(\+34|0034|34)?[\s\-]?(6|7|9)[\s\-]?([0-9]{1,4})[\s\-]?([0-9]{1,4})[\s\-]?([0-9]{0,4})$/', // Formatos españoles
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed', // Requiere password_confirmation
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', // Al menos 1 minúscula, 1 mayúscula, 1 número
            ],
        ];
    }

    /**
     * Obtiene los mensajes personalizados para los errores de validación.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios y apóstrofes.',
            'nombre.max' => 'El nombre no puede exceder 100 caracteres.',

            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras, espacios y apóstrofes.',
            'apellidos.max' => 'Los apellidos no pueden exceder 100 caracteres.',

            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'username.min' => 'El nombre de usuario debe tener al menos 3 caracteres.',
            'username.max' => 'El nombre de usuario no puede exceder 50 caracteres.',
            'username.regex' => 'El nombre de usuario solo puede contener letras, números, guiones, puntos y guiones bajos.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'email.max' => 'El correo electrónico no puede exceder 150 caracteres.',

            'dni_nie.required' => 'El DNI/NIE es obligatorio.',
            'dni_nie.unique' => 'Este DNI/NIE ya está registrado.',
            'dni_nie.regex' => 'El DNI/NIE debe tener el formato correcto (ej: 12345678A, X1234567L).',
            'dni_nie.max' => 'El DNI/NIE no puede exceder 15 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.unique' => 'Este teléfono ya está registrado.',
            'telefono.regex' => 'El teléfono debe ser un número español válido (ej: 600000000, +34 600 000 000).',
            'telefono.max' => 'El teléfono no puede exceder 20 caracteres.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.max' => 'La contraseña no puede exceder 255 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ];
    }

    /**
     * Prepara los datos para la validación.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar teléfono (eliminar espacios y guiones)
        $this->merge([
            'telefono' => preg_replace('/[\s\-]/', '', $this->telefono ?? ''),
            'dni_nie' => strtoupper($this->dni_nie ?? ''),
        ]);
    }
}
