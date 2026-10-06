<?php

namespace App\Http\Requests\Admin;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true;
    }

    public function rules(): array
    {
        $creating = $this->isMethod('post');

        return [
            'title' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category' => ['required', Rule::in(array_keys(Resource::categories()))],
            'type' => ['required', Rule::in(array_keys(Resource::types()))],
            'external_url' => ['nullable', 'url', 'max:255', 'required_if:type,link', 'required_if:type,video'],
            'author' => ['nullable', 'string', 'max:120'],
            'published_on' => ['nullable', 'date'],
            'visibility' => ['required', Rule::in(['public', 'members'])],
            'file' => [$creating ? 'required_if:type,file' : 'nullable', 'file', 'max:8192'],
            'thumbnail' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,webp,gif'],
        ];
    }
}
