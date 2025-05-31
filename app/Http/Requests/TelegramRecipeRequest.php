<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TelegramRecipeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required', 'string'],
            'message_id' => ['nullable', 'integer'],
            'channel' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
