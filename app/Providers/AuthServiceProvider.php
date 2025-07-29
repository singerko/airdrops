<?php
// app/Providers/AuthServiceProvider.php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Airdrop;
use App\Models\Project;
use App\Policies\AirdropPolicy;
use App\Policies\ProjectPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Airdrop::class => AirdropPolicy::class,
        Project::class => ProjectPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Admin gate
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Airdrop management gates
        Gate::define('manage-airdrops', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-projects', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->isAdmin();
        });
    }
}
