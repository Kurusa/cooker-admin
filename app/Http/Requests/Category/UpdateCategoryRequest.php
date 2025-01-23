<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'parentId' => 'nullable|exists:categories,id',
            'mergeCategories' => 'array|exists:categories,id',
            'childCategories' => 'array|exists:categories,id',
        ];
    }
}
