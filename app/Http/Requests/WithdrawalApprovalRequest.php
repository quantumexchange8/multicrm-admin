<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalApprovalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => ['required']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes()
    {
        return [
            'comment' => 'Remarks'
        ];
    }
}
