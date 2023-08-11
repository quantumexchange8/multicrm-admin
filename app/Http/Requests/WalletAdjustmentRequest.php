<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletAdjustmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'comment' => 'nullable|string'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'amount' => 'Adjust Amount (USD)',
            'comment' => 'Description'
        ];
    }
}
