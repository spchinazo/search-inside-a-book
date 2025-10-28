<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => 'required|string|min:2|max:200',
            'page' => 'sometimes|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'q.required' => 'The search query is required.',
            'q.min' => 'The search query must be at least 2 characters.',
            'q.max' => 'The search query must not exceed 200 characters.',
            'page.integer' => 'The page must be a valid number.',
            'page.min' => 'The page must be at least 1.',
        ];
    }
}