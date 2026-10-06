<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeMember extends Model
{
    protected $fillable = [
        'executive_committee_id',
        'committee_position_id',
        'user_id',
        'name',
        'photo',
        'department',
        'batch',
        'bio',
        'linkedin',
        'facebook',
        'other_link',
        'sort_order',
    ];

    public function committee(): BelongsTo
    {
        return $this->belongsTo(ExecutiveCommittee::class, 'executive_committee_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(CommitteePosition::class, 'committee_position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
