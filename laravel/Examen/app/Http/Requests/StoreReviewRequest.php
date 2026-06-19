<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'title' => 'nullable|string|max:100',
            'content' => 'required|string|min:10|max:1000',
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
            'title.max' => 'El título no puede superar los 100 caracteres',
            'content.required' => 'El contenido de la reseña es obligatorio',
            'content.min' => 'La reseña debe tener al menos 10 caracteres',
            'content.max' => 'La reseña no puede superar los 1000 caracteres',
        ];
    }
}
