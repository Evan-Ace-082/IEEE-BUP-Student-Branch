<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GalleryAlbumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'gallery_category_id' => ['nullable', 'exists:gallery_categories,id'],
            'event_id' => ['nullable', 'exists:events,id'],
            'images' => ['nullable', 'array', 'max:12'],
            'images.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
            'new_category' => ['nullable', 'string', 'max:80'],
        ];
    }
}
