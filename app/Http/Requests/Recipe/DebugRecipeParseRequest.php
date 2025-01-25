<?php

namespace App\Http\Requests\Recipe;

use Illuminate\Foundation\Http\FormRequest;

class DebugRecipeParseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url'       => 'required|url',
            'source_id' => 'required|exists:sources,id',
        ];
    }
}
