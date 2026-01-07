<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    /**
     * @queryParam q string required El término de búsqueda. Example: DOM
     * @queryParam page int Página de resultados. Example: 1
     * @queryParam per_page int Cantidad de resultados por página (max 100). Example: 10
     */
    public function rules(): array
    {
        return [
            'q' => 'required|string|min:1',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
