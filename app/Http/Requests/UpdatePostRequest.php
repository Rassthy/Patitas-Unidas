<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'category_id' => 'sometimes|required|integer|exists:forum_categories,id',
            'titulo' => 'sometimes|required|string|max:200',
            'descripcion' => 'sometimes|required|string',
            'provincia' => 'sometimes|required|string|max:50',
            'ciudad' => 'sometimes|required|string|max:100',
            'animal_nombre' => 'nullable|string|max:100',
            'animal_especie' => 'nullable|string|max:50',
            'animal_raza' => 'nullable|string|max:50',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'estado' => ['sometimes', Rule::in(['activa', 'en_revision', 'cerrada'])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'La categoría es obligatoria',
            'category_id.exists' => 'La categoría seleccionada no existe',
            'titulo.required' => 'El título es obligatorio',
            'titulo.max' => 'El título no puede exceder 200 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'provincia.required' => 'La provincia es obligatoria',
            'ciudad.required' => 'La ciudad es obligatoria',
            'estado.in' => 'El estado debe ser: activa, en_revision o cerrada',
        ];
    }
}
