<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAnyAdmin(User $actor): bool
    {
        return $actor->isSuperAdmin();
    }

    public function manageAdmin(User $actor, User $subject): bool
    {
        return $actor->isSuperAdmin()
            && $subject->isAdmin()
            && $actor->id !== $subject->id;
    }

    public function createAdmin(User $actor): bool
    {
        return $actor->isSuperAdmin();
    }
}
