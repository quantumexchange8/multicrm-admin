<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetMemberPasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', Password::min(6)->letters()->mixedCase()->numbers()],
            'password_confirmation' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function attributes()
    {
        return [
            'password' => 'Password',
            'password_confirmation' => 'Confirm Password'
        ];
    }
}
