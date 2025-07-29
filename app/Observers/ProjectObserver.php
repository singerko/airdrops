<?php
// app/Observers/ProjectObserver.php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    public function deleting(Project $project)
    {
        // Prevent deletion if project has airdrops
        if ($project->airdrops()->count() > 0) {
            throw new \Exception('Cannot delete project with existing airdrops.');
        }
    }
}
