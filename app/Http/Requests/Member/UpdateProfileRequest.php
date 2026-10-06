<?php

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->isMember() && $user->isActiveAccount();
    }

    public function rules(): array
    {
        $profileId = $this->user()?->profile?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'student_id' => ['nullable', 'string', 'max:40', Rule::unique('member_profiles', 'student_id')->ignore($profileId)],
            'department' => ['nullable', 'string', 'max:120'],
            'batch' => ['nullable', 'string', 'max:40'],
            'session' => ['nullable', 'string', 'max:40'],
            'ieee_membership_id' => ['nullable', 'string', 'max:40'],
            'ieee_membership_status' => ['required', 'in:none,student,graduate,professional'],
            'skills' => ['nullable', 'string', 'max:500'],
            'interests' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{6,20}$/'],
            'show_email' => ['nullable', 'boolean'],
            'show_phone' => ['nullable', 'boolean'],
            'show_social' => ['nullable', 'boolean'],
            'directory_visible' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }
}
