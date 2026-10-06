<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'student_id',
        'email',
        'phone',
        'department',
        'batch',
        'ieee_membership_status',
        'status',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public static function statuses(): array
    {
        return [
            'registered' => 'Registered',
            'cancelled' => 'Cancelled',
            'attended' => 'Attended',
            'no_show' => 'No Show',
        ];
    }
}
