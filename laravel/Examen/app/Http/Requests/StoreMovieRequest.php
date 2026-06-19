<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'year' => 'required|integer|min:1900|max:' . date('Y'),
            'genre' => 'required|string|max:100',
            'duration' => 'required|integer|min:1',
            'synopsis' => 'required|string|min:10',
            'cast' => 'nullable|string|max:500',
            'country' => 'nullable|string|max:100',
            'age_rating' => 'nullable|string|max:10',
            'poster' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no puede superar los 255 caracteres',
            'director.required' => 'El director es obligatorio',
            'year.required' => 'El año es obligatorio',
            'year.integer' => 'El año debe ser un número',
            'year.min' => 'El año debe ser mayor a 1900',
            'year.max' => 'El año no puede ser mayor al año actual',
            'genre.required' => 'El género es obligatorio',
            'duration.required' => 'La duración es obligatoria',
            'duration.integer' => 'La duración debe ser un número',
            'duration.min' => 'La duración debe ser al menos 1 minuto',
            'synopsis.required' => 'La sinopsis es obligatoria',
            'synopsis.min' => 'La sinopsis debe tener al menos 10 caracteres',
            'poster.image' => 'El archivo debe ser una imagen',
            'poster.mimes' => 'La imagen debe ser de tipo: jpeg, jpg, png o gif',
            'poster.max' => 'La imagen no puede superar los 2MB',
        ];
    }
}
