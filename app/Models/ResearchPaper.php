<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchPaper extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'authors',
        'abstract',
        'category',
        'keywords',
        'publication_year',
        'publication_status',
        'doi',
        'external_url',
        'citation',
        'supplementary',
        'pdf_path',
        'review_status',
        'is_published',
        'review_note',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'publication_year' => 'integer',
            'is_published' => 'boolean',
            'reviewed_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by')->withTrashed();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withTrashed();
    }

    public static function categories(): array
    {
        return [
            'computing' => 'Computing',
            'electrical' => 'Electrical engineering',
            'electronics' => 'Electronics',
            'communications' => 'Communications',
            'interdisciplinary' => 'Interdisciplinary',
            'other' => 'Other',
        ];
    }

    public static function publicationStatuses(): array
    {
        return [
            'published' => 'Published',
            'accepted' => 'Accepted',
            'preprint' => 'Preprint',
        ];
    }

    public static function reviewStatuses(): array
    {
        return [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
    }

    public function isPubliclyVisible(): bool
    {
        return $this->review_status === 'approved' && $this->is_published;
    }

    public function canBeReadBy(?User $user): bool
    {
        if ($this->isPubliclyVisible()) {
            return true;
        }

        if (! $user || ! $user->isActiveAccount()) {
            return false;
        }

        return $user->hasAdminAccess() || $user->id === $this->submitted_by;
    }

    public function reviewLabel(): string
    {
        return self::reviewStatuses()[$this->review_status] ?? $this->review_status;
    }

    public function doiUrl(): ?string
    {
        $doi = trim((string) $this->doi);
        if ($doi === '' || ! preg_match('/^10\.\d{4,9}\/\S+$/', $doi)) {
            return null;
        }

        return 'https://doi.org/'.$doi;
    }
}
