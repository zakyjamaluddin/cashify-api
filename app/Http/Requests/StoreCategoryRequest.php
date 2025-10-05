<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:Income,Expense',
            'icon' => 'required|string|max:50',
            'color' => 'sometimes|string|size:7',
        ];
    }
}



