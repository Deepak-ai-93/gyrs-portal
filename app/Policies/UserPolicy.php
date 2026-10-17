<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $candidate): bool
    {
        return $user->isAdmin()
            || ($user->isCandidate() && $user->getKey() === $candidate->getKey());
    }

    public function update(User $user, User $candidate): bool
    {
        return $user->isAdmin()
            || ($user->isCandidate() && $user->getKey() === $candidate->getKey());
    }

    public function updateStatus(User $user, User $candidate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $candidate): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin();
    }
}
