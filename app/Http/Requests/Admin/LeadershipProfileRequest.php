<?php

namespace App\Http\Requests\Admin;

use App\Models\LeadershipProfile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadershipProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true && $this->user()->isActiveAccount();
    }

    public function rules(): array
    {
        return [
            'role' => ['required', Rule::in(array_keys(LeadershipProfile::roleOptions()))],
            'position_label' => ['nullable', 'required_if:role,other', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:160'],
            'designation' => ['nullable', 'string', 'max:180'],
            'department' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:30'],
            'show_email' => ['nullable', 'boolean'],
            'show_phone' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['required', 'boolean'],
            'photo' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }
}
