<?php

namespace App\Http\Requests;

use App\Support\HumanCheck;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'student_id' => ['nullable', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{6,20}$/'],
            'department' => ['nullable', 'string', 'max:120'],
            'batch' => ['nullable', 'string', 'max:40'],
            'ieee_membership_status' => ['required', 'in:none,student,graduate,professional'],
            'human_answer' => ['required', 'string', 'max:10'],
            'company_website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_website.prohibited' => 'The form could not be submitted.',
            'phone.regex' => 'Enter a valid phone number.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! HumanCheck::passes($this->input('human_answer'))) {
                $validator->errors()->add('human_answer', 'Enter the correct answer to the addition check.');
            }
        });
    }
}
