<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
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
            'category_id' => 'required|integer|exists:forum_categories,id',
            'titulo' => 'required|string|max:200',
            'descripcion' => 'required|string',
            'provincia' => 'required|string|max:50',
            'ciudad' => 'required|string|max:100',
            'animal_nombre' => 'nullable|string|max:100',
            'animal_especie' => 'nullable|string|max:50',
            'animal_raza' => 'nullable|string|max:50',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'estado' => ['required', Rule::in(['activa', 'en_revision', 'cerrada'])],
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
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
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser: activa, en_revision o cerrada',
            'images.max' => 'No puedes cargar más de 10 imágenes',
            'images.*.image' => 'Cada archivo debe ser una imagen válida',
            'images.*.mimes' => 'Las imágenes deben ser jpeg, png, jpg o gif',
            'images.*.max' => 'Cada imagen no puede exceder 2MB',
        ];
    }
}
