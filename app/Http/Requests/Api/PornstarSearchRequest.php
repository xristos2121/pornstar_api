<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PornstarSearchRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'q' => 'nullable|string|min:2',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'sort' => 'nullable|string',
            'filter' => 'nullable|array',
            'filter.hair_color' => 'nullable|string',
            'filter.ethnicity' => 'nullable|string',
            'filter.age' => 'nullable|string', // Format: "18..30"
            'filter.height' => 'nullable|string', // Format: "150..180"
            'filter.weight' => 'nullable|string', // Format: "45..70"
            'filter.videos' => 'nullable|string', // Format: "10..*"
            'filter.views' => 'nullable|string', // Format: "1000..*"
            'filter.rank' => 'nullable|string', // Format: "*..100"
        ];
    }
}
