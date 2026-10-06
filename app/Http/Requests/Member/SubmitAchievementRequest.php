<?php

namespace App\Http\Requests\Member;

use App\Models\Achievement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitAchievementRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->isMember() && $user->isActiveAccount();
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
            'image' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
        ];
    }
}
