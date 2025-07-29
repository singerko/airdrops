<?php
// app/Policies/ProjectPolicy.php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Project $project)
    {
        return $project->is_active || $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Project $project)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Project $project)
    {
        return $user->isAdmin() && $project->airdrops()->count() === 0;
    }
}
