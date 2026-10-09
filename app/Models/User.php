<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Role is loaded with the account so authorization checks do not N+1.
     *
     * @var list<string>
     */
    protected $with = ['role'];

    /**
     * Privileged columns (role_id, status) are intentionally not fillable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function submittedAchievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'submitted_by');
    }

    public function researchPapers(): HasMany
    {
        return $this->hasMany(ResearchPaper::class, 'submitted_by');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by')->withTrashed();
    }

    public function roleSlug(): string
    {
        return (string) ($this->role?->slug ?? '');
    }

    public function isSuperAdmin(): bool
    {
        return $this->roleSlug() === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->roleSlug() === 'admin';
    }

    public function isMember(): bool
    {
        return $this->roleSlug() === 'member';
    }

    public function hasAdminAccess(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    public function isActiveAccount(): bool
    {
        return $this->status === 'active';
    }

    public function scopeWithRole($query, string $slug)
    {
        return $query->whereHas('role', fn ($q) => $q->where('slug', $slug));
    }

    public function scopeMembers($query)
    {
        return $query->withRole('member');
    }

    public function scopeAdmins($query)
    {
        return $query->withRole('admin');
    }
}
