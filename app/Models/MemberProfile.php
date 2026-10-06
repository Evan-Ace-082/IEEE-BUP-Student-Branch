<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberProfile extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'department',
        'batch',
        'session',
        'ieee_membership_id',
        'ieee_membership_status',
        'photo',
        'skills',
        'interests',
        'bio',
        'linkedin',
        'facebook',
        'phone',
        'show_email',
        'show_phone',
        'show_social',
        'directory_visible',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'interests' => 'array',
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'show_social' => 'boolean',
            'directory_visible' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function skillList(): string
    {
        return implode(', ', $this->skills ?? []);
    }

    public function interestList(): string
    {
        return implode(', ', $this->interests ?? []);
    }
}
