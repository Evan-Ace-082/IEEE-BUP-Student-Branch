<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'person_or_team',
        'category',
        'achieved_on',
        'image',
        'link',
        'status',
        'is_featured',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'achieved_on' => 'date',
            'reviewed_at' => 'datetime',
            'is_featured' => 'boolean',
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

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public static function categories(): array
    {
        return [
            'branch' => 'Branch Achievement',
            'member' => 'Member Achievement',
            'award' => 'Award',
            'competition' => 'Competition',
            'hackathon' => 'Hackathon',
            'research' => 'Research',
            'publication' => 'Publication',
            'other' => 'Other',
        ];
    }

    public static function statuses(): array
    {
        return [
            'submitted' => 'Submitted',
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'published' => 'Published',
            'rejected' => 'Rejected',
        ];
    }
}
