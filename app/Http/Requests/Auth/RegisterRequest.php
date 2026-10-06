<?php

namespace App\Http\Requests\Auth;

use App\Support\HumanCheck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'human_answer' => ['required', 'string', 'max:10'],
            'company_website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_website.prohibited' => 'The form could not be submitted.',
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
