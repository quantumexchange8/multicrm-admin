<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'type' => ['required'],
            'internal_description' => ['required'],
            'client_description' => ['required'],
            'start_date' => ['required_if:allotted_time,1'],
            'end_date' => ['required_if:allotted_time,1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'amount' => 'Amount',
            'type' => 'Type',
            'internal_description' => 'Internal Description',
            'client_description' => 'Description (Visible to Client)',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
        ];
    }
}
