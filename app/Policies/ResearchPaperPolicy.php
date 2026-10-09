<?php

namespace App\Policies;

use App\Models\ResearchPaper;
use App\Models\User;

class ResearchPaperPolicy
{
    public function submit(User $user): bool
    {
        return $user->isActiveAccount();
    }

    public function review(User $user, ?ResearchPaper $paper = null): bool
    {
        return $user->hasAdminAccess() && $user->isActiveAccount();
    }
}
