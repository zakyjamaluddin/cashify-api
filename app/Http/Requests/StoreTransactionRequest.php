<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wallet_id' => 'required|uuid',
            'type' => 'required|in:Income,Expense',
            'category_id' => 'required|uuid',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'proof_url' => 'nullable|url|max:500',
            'is_recurring' => 'boolean',
            'recurring_schedule_id' => 'nullable|uuid',
        ];
    }
}



