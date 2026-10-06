<?php

namespace App\Policies;

use App\Models\MemberProfile;
use App\Models\User;

class MemberProfilePolicy
{
    public function update(User $actor, MemberProfile $profile): bool
    {
        return $actor->isMember()
            && $actor->isActiveAccount()
            && $actor->id === $profile->user_id;
    }
}
