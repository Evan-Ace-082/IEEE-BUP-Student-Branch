<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExecutiveCommittee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'year',
        'is_current',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_current' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(CommitteeMember::class);
    }
}
