<?php

namespace App\Http\Requests;

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
            'name' => 'sometimes|string|max:100',
            'type' => 'sometimes|in:Income,Expense',
            'icon' => 'sometimes|string|max:50',
            'color' => 'sometimes|string|size:7',
        ];
    }
}



