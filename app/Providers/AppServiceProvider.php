<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\Airdrop;
use App\Models\Project;
use App\Models\Blockchain;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind services
        $this->app->singleton(\App\Services\WalletAuthService::class);
        $this->app->singleton(\App\Services\TranslationService::class);
        $this->app->singleton(\App\Services\NotificationService::class);
    }

    public function boot()
    {
        // Set default string length for database
        Schema::defaultStringLength(191);

        // Use Bootstrap pagination views
        Paginator::useBootstrap();

        // Share global data with views
        View::composer('*', function ($view) {
            if (!app()->runningInConsole()) {
                $view->with([
                    'currentUser' => auth()->user(),
                ]);
            }
        });

        // Share stats with home page
        View::composer('home', function ($view) {
            $view->with([
                'stats' => [
                    'total_airdrops' => Airdrop::published()->count(),
                    'active_airdrops' => Airdrop::published()->active()->count(),
                    'total_projects' => Project::active()->count(),
                    'total_blockchains' => Blockchain::active()->count(),
                ],
                'featured_airdrops' => Airdrop::published()
                    ->featured()
                    ->with(['project', 'blockchain'])
                    ->orderBy('priority', 'desc')
                    ->limit(6)
                    ->get(),
                'latest_airdrops' => Airdrop::published()
                    ->with(['project', 'blockchain'])
                    ->latest('published_at')
                    ->limit(8)
                    ->get(),
                'ending_soon' => Airdrop::published()
                    ->endingSoon(7)
                    ->with(['project', 'blockchain'])
                    ->orderBy('ends_at')
                    ->limit(6)
                    ->get(),
                'starting_soon' => Airdrop::published()
                    ->startingSoon(7)
                    ->with(['project', 'blockchain'])
                    ->orderBy('starts_at')
                    ->limit(6)
                    ->get(),
            ]);
        });

        // Model observers
        Airdrop::observe(\App\Observers\AirdropObserver::class);
        Project::observe(\App\Observers\ProjectObserver::class);
    }
}
