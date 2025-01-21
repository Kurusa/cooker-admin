<?php

namespace App\Http\Requests\Source;

use Illuminate\Foundation\Http\FormRequest;

class CreateSitemapUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => 'required|url',
        ];
    }
}
