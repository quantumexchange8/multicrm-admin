<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'group' => ['required'],
            'minimum_deposit' => ['required'],
            'leverage' => ['required'],
            'currency' => ['required'],
            'allow_create_account' => ['required'],
            'commission_structure' => ['required'],
            'trade_open_duration' => ['required'],
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Account Type',
            'group' => 'Group Name',
            'minimum_deposit' => 'Minimum Deposit',
            'leverage' => 'Leverage',
            'currency' => 'Currency',
            'allow_create_account' => 'Allow New Account Creation',
            'commission_structure' => 'Commission Structure',
            'trade_open_duration' => 'Trade Open Duration',
        ];
    }
}
