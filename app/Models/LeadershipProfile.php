<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadershipProfile extends Model
{
    protected $fillable = [
        'role',
        'name',
        'designation',
        'bio',
        'photo',
        'email',
        'phone',
        'show_email',
        'show_phone',
        'sort_order',
        'is_published',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'show_email' => 'boolean',
            'show_phone' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }

    public static function roleOptions(): array
    {
        return [
            'chairman_faculty_advisor' => 'Chairman / Faculty Advisor',
            'chairman' => 'Chairman',
            'faculty_advisor' => 'Faculty Advisor',
            'moderator' => 'Moderator',
            'co_moderator' => 'Co-Moderator',
        ];
    }

    /**
     * Published profiles in display order, followed by placeholders for roles the branch has not confirmed yet.
     *
     * @return list<array{placeholder: bool, name: string, designation: string, bio: string, photo: ?string, email: ?string, phone: ?string}>
     */
    public static function publicEntries(): array
    {
        $published = self::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $entries = [];
        foreach ($published as $profile) {
            $entries[] = self::entryFromProfile($profile);
        }

        foreach (self::missingPlaceholders($published->pluck('role')->unique()->all()) as $placeholder) {
            $entries[] = $placeholder;
        }

        return $entries;
    }

    /**
     * @param  list<string>  $rolesPresent
     * @return list<array{placeholder: bool, name: string, designation: string, bio: string, photo: ?string, email: ?string, phone: ?string}>
     */
    private static function missingPlaceholders(array $rolesPresent): array
    {
        $hasChair = in_array('chairman', $rolesPresent, true);
        $hasAdvisor = in_array('faculty_advisor', $rolesPresent, true);
        $hasCombined = in_array('chairman_faculty_advisor', $rolesPresent, true);
        $placeholders = [];

        if ($hasChair || $hasAdvisor) {
            if (! $hasChair) {
                $placeholders[] = self::placeholder('chairman');
            }
            if (! $hasAdvisor) {
                $placeholders[] = self::placeholder('faculty_advisor');
            }
        } elseif (! $hasCombined) {
            $placeholders[] = self::placeholder('chairman_faculty_advisor');
        }

        if (! in_array('moderator', $rolesPresent, true)) {
            $placeholders[] = self::placeholder('moderator');
        }

        if (! in_array('co_moderator', $rolesPresent, true)) {
            $placeholders[] = self::placeholder('co_moderator');
        }

        return $placeholders;
    }

    private static function entryFromProfile(self $profile): array
    {
        $designation = trim((string) $profile->designation);

        return [
            'placeholder' => false,
            'name' => $profile->name,
            'designation' => $designation !== '' ? $designation : (self::roleOptions()[$profile->role] ?? 'Leadership'),
            'bio' => trim((string) $profile->bio) !== ''
                ? $profile->bio
                : 'Biography will be added when the branch publishes this profile.',
            'photo' => $profile->photo,
            'email' => $profile->show_email && $profile->email ? $profile->email : null,
            'phone' => $profile->show_phone && $profile->phone ? $profile->phone : null,
        ];
    }

    private static function placeholder(string $role): array
    {
        return [
            'placeholder' => true,
            'name' => 'To be announced',
            'designation' => self::roleOptions()[$role] ?? 'Leadership',
            'bio' => 'Biography will be added when the branch publishes this profile.',
            'photo' => null,
            'email' => null,
            'phone' => null,
        ];
    }
}
