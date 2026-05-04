<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $emailValidation = app()->environment('testing')
            ? 'required|email|max:150|unique:users,email'
            : 'required|email:rfc,dns|max:150|unique:users,email';

        $isOrg = $this->input('tipo') === 'organizacion';

        if ($isOrg) {
            return [
                'tipo'               => 'required|in:organizacion',
                'nombre_organizacion'=> 'required|string|max:150',
                'tipo_organizacion'  => 'required|in:protectora,veterinaria,refugio,asociacion',
                'username'           => ['required','string','min:3','max:50','unique:users,username','regex:/^[a-zA-Z0-9_\-\.]+$/'],
                'email'              => $emailValidation,
                'cif'                => ['required','string','max:15','unique:users,cif','regex:/^[A-Z][0-9]{7}[A-Z0-9]$/'],
                'telefono'           => ['required','string','max:20','unique:users,telefono'],
                'provincia'          => 'nullable|string|max:50',
                'ciudad'             => 'nullable|string|max:100',
                'direccion'          => 'nullable|string|max:200',
                'web'                => 'nullable|url|max:200',
                'password'           => ['required','string','min:8','max:255','confirmed','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'],
            ];
        }

        return [
            'tipo'      => 'required|in:usuario',
            'nombre'    => ['required','string','max:100','regex:/^[a-záéíóúñ\s\-\']+$/i'],
            'apellidos' => ['required','string','max:100','regex:/^[a-záéíóúñ\s\-\']+$/i'],
            'username'  => ['required','string','min:3','max:50','unique:users,username','regex:/^[a-zA-Z0-9_\-\.]+$/'],
            'email'     => $emailValidation,
            'dni_nie'   => ['required','string','max:15','unique:users,dni_nie','regex:/^[0-9]{8}[A-Z]$|^[XYZ][0-9]{7}[A-Z]$/'],
            'telefono'  => ['required','string','max:20','unique:users,telefono','regex:/^(\+34|0034|34)?[\s\-]?(6|7|9)[\s\-]?([0-9]{8})$/'],
            'password'  => ['required','string','min:8','max:255','confirmed','regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'            => 'El nombre es obligatorio.',
            'nombre.regex'               => 'El nombre solo puede contener letras y espacios.',
            'apellidos.required'         => 'Los apellidos son obligatorios.',
            'apellidos.regex'            => 'Los apellidos solo pueden contener letras y espacios.',
            'username.required'          => 'El nombre de usuario es obligatorio.',
            'username.unique'            => 'Este nombre de usuario ya está en uso.',
            'username.regex'             => 'El nombre de usuario solo puede contener letras, números, guiones y puntos.',
            'email.required'             => 'El correo electrónico es obligatorio.',
            'email.unique'               => 'Este correo ya está registrado.',
            'dni_nie.required'           => 'El DNI/NIE es obligatorio.',
            'dni_nie.unique'             => 'Este DNI/NIE ya está registrado.',
            'dni_nie.regex'              => 'El DNI/NIE debe tener el formato correcto (ej: 12345678A).',
            'cif.required'               => 'El CIF es obligatorio.',
            'cif.unique'                 => 'Este CIF ya está registrado.',
            'cif.regex'                  => 'El CIF debe tener el formato correcto (ej: A12345678).',
            'telefono.required'          => 'El teléfono es obligatorio.',
            'telefono.unique'            => 'Este teléfono ya está registrado.',
            'nombre_organizacion.required' => 'El nombre de la organización es obligatorio.',
            'tipo_organizacion.required' => 'El tipo de organización es obligatorio.',
            'password.required'          => 'La contraseña es obligatoria.',
            'password.min'               => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex'             => 'La contraseña debe contener mayúscula, minúscula y número.',
            'password.confirmed'         => 'Las contraseñas no coinciden.',
            'web.url'                    => 'La URL de la web no es válida.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->telefono) $merge['telefono'] = preg_replace('/[\s\-]/', '', $this->telefono);
        if ($this->dni_nie)  $merge['dni_nie']  = strtoupper($this->dni_nie);
        if ($this->cif)      $merge['cif']      = strtoupper($this->cif);
        if ($merge) $this->merge($merge);
    }
}