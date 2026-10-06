<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner',
        'starts_at',
        'ends_at',
        'venue',
        'speaker',
        'organizer',
        'category',
        'registration_enabled',
        'registration_deadline',
        'status',
        'is_featured',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'registration_enabled' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(GalleryAlbum::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeUpcoming($query)
    {
        return $query->published()->where('starts_at', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->published()
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->published()->where('ends_at', '<', now());
    }

    public function temporalStatus(): string
    {
        if ($this->status !== 'published') {
            return (string) $this->status;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return 'upcoming';
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            return 'past';
        }

        return 'ongoing';
    }

    public function registrationOpen(): bool
    {
        if (! $this->registration_enabled || $this->status !== 'published') {
            return false;
        }

        if (in_array($this->temporalStatus(), ['past', 'cancelled'], true)) {
            return false;
        }

        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        return true;
    }

    public static function categories(): array
    {
        return [
            'workshop' => 'Workshop',
            'seminar' => 'Seminar',
            'webinar' => 'Webinar',
            'competition' => 'Competition',
            'conference' => 'Conference',
            'training' => 'Training',
            'technical_session' => 'Technical Session',
            'awareness' => 'Awareness Program',
            'other' => 'Other',
        ];
    }
}
