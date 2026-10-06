<?php

namespace App\Http\Requests\Admin;

use App\Models\Achievement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'person_or_team' => ['nullable', 'string', 'max:180'],
            'category' => ['required', Rule::in(array_keys(Achievement::categories()))],
            'achieved_on' => ['nullable', 'date'],
            'link' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Achievement::statuses()))],
            'is_featured' => ['nullable', 'boolean'],
            'review_note' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }
}
