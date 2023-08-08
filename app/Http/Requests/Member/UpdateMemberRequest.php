<?php

namespace App\Http\Requests\Member;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function rules(): array
    {
        $user_id = $this->request->get('user_id');

        return [
            'first_name' => ['required', 'max:255'],
            'chinese_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['date'],
            'country' => ['required', 'max:255'],
            'phone' => ['required', 'max:255', Rule::unique(User::class)->ignore($user_id)],
            'kyc_approval_description' => ['required_if:kyc_approval,approve,reject'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'Full Name',
            'chinese_name' => 'Chinese Name',
            'dob' => 'Date of Birth',
            'country' => 'Country',
            'phone' => 'Mobile Phone',
            'kyc_approval' => 'KYC Approval',
            'kyc_approval_description' => 'KYC Approval Description',
        ];
    }
}
