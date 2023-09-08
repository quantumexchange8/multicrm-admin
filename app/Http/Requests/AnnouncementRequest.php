<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
            'title' => 'required',
            'content' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recipient' => 'required',
            'image' => 'nullable|image',
            'popup' => 'nullable',
            'popup_daily' => 'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Title',
            'content' => 'Announcement Details',
            'start_date' => 'Post Date',
            'end_date' => 'Expired Date',
            'recipient' => 'Trigger Email',
            'image' => 'Upload Document',
            'popup' => 'Trigger Email',
            'popup_daily' => 'nullable',
        ];
    }
}
