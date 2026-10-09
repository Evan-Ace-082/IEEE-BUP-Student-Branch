<?php

namespace App\Policies;

use App\Models\LeadershipProfile;
use App\Models\User;

class LeadershipProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->manage($user);
    }

    public function create(User $user): bool
    {
        return $this->manage($user);
    }

    public function update(User $user, LeadershipProfile $profile): bool
    {
        return $this->manage($user);
    }

    public function delete(User $user, LeadershipProfile $profile): bool
    {
        return $this->manage($user);
    }

    private function manage(User $user): bool
    {
        return $user->hasAdminAccess() && $user->isActiveAccount();
    }
}
