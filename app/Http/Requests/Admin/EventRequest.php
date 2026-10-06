<?php

namespace App\Http\Requests\Admin;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:20000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'start_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'venue' => ['nullable', 'string', 'max:180'],
            'speaker' => ['nullable', 'string', 'max:180'],
            'organizer' => ['nullable', 'string', 'max:180'],
            'category' => ['required', Rule::in(array_keys(Event::categories()))],
            'registration_enabled' => ['nullable', 'boolean'],
            'registration_deadline' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled'])],
            'is_featured' => ['nullable', 'boolean'],
            'banner' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'remove_banner' => ['nullable', 'boolean'],
        ];
    }
}
