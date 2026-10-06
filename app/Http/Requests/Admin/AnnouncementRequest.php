<?php

namespace App\Http\Requests\Admin;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'content' => ['required', 'string', 'max:20000'],
            'category' => ['required', Rule::in(array_keys(Announcement::categories()))],
            'status' => ['required', Rule::in(array_keys(Announcement::statuses()))],
            'featured_image' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }
}
