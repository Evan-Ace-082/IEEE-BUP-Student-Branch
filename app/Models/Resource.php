<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category',
        'type',
        'disk',
        'file_path',
        'external_url',
        'thumbnail',
        'author',
        'published_on',
        'visibility',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public static function categories(): array
    {
        return [
            'ieee' => 'IEEE Resources',
            'learning' => 'Learning Materials',
            'workshop' => 'Workshop Materials',
            'slides' => 'Presentation Slides',
            'research' => 'Research Resources',
            'recordings' => 'Event Recordings',
            'links' => 'Useful Links',
            'other' => 'Other',
        ];
    }

    public static function types(): array
    {
        return [
            'file' => 'PDF / file',
            'link' => 'External link',
            'video' => 'Video link',
        ];
    }
}
