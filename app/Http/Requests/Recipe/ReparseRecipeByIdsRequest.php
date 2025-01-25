<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class ReparseRecipeByIdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipe_ids'   => 'required|array|min:1',
            'recipe_ids.*' => 'integer|exists:recipes,id',
        ];
    }
}
