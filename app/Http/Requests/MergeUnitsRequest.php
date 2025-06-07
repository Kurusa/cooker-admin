<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MergeUnitsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'main_unit_id' => 'required|exists:units,id',
            'merge_unit_ids' => 'required|array',
            'merge_unit_ids.*' => 'exists:units,id|different:main_unit_id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
