<?php

namespace App\Policies;

use App\Models\ResumeAnalysis;
use App\Models\User;

class ResumeAnalysisPolicy
{
    public function view(User $user, ResumeAnalysis $analysis): bool
    {
        return $user->getKey() === $analysis->user_id;
    }

    public function update(User $user, ResumeAnalysis $analysis): bool
    {
        return $this->view($user, $analysis);
    }

    public function delete(User $user, ResumeAnalysis $analysis): bool
    {
        return $this->view($user, $analysis);
    }
}
