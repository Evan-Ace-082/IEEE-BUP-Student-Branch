<?php

namespace App\Http\Requests\Admin;

use App\Models\ResearchPaper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResearchPaperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAdminAccess() === true && $this->user()->isActiveAccount();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:220'],
            'authors' => ['required', 'string', 'max:500'],
            'abstract' => ['required', 'string', 'max:10000'],
            'category' => ['required', Rule::in(array_keys(ResearchPaper::categories()))],
            'keywords' => ['nullable', 'string', 'max:500'],
            'publication_year' => ['required', 'integer', 'min:1900', 'max:'.((int) date('Y') + 1)],
            'publication_status' => ['required', Rule::in(array_keys(ResearchPaper::publicationStatuses()))],
            'doi' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'citation' => ['nullable', 'string', 'max:1000'],
            'supplementary' => ['nullable', 'string', 'max:5000'],
            'pdf' => ['nullable', 'file', 'max:8192', 'mimes:pdf'],
        ];
    }
}
