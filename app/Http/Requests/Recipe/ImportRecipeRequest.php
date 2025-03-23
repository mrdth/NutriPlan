<?php

declare(strict_types=1);

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class ImportRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $parsed = parse_url($value);
                    if (empty($parsed['scheme']) || !in_array($parsed['scheme'], ['http', 'https'])) {
                        $fail('The URL must use either http or https protocol.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'url.required' => 'Please enter a recipe URL.',
            'url.url' => 'Please enter a valid URL.',
            'url.max' => 'The URL is too long. Maximum length is 2048 characters.',
        ];
    }
}
