<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wallet_id' => 'sometimes|uuid',
            'type' => 'sometimes|in:Income,Expense',
            'category_id' => 'sometimes|uuid',
            'amount' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'sometimes|date',
            'proof_url' => 'nullable|url|max:500',
            'is_recurring' => 'boolean',
            'recurring_schedule_id' => 'nullable|uuid',
        ];
    }
}



