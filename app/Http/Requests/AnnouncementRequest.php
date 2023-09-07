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
        $rules = [
            'title' => 'required',
            'content' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'recipient' => 'required',
            'popup' => 'nullable',
            'popup_daily' => 'nullable',
        ];

        // Check the value of the 'request_type' field (you may adjust the field name as needed)
        // 'create' indicates a create request, 'edit' indicates an edit request
        if ($this->input('request_type') === 'edit') {
            // For edit, make the 'image' field nullable
            $rules['image'] = 'nullable|image';
        } else {
            // For create, make the 'image' field required
            $rules['image'] = 'required|image';
        }

        return $rules;
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
