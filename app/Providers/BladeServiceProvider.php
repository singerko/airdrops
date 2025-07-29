<?php
// app/Providers/BladeServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Custom directive for checking if user is admin
        Blade::directive('admin', function () {
            return "<?php if(auth()->check() && auth()->user()->isAdmin()): ?>";
        });

        Blade::directive('endadmin', function () {
            return "<?php endif; ?>";
        });

        // Custom directive for formatting numbers
        Blade::directive('number', function ($expression) {
            return "<?php echo number_format($expression); ?>";
        });

        // Custom directive for time ago
        Blade::directive('timeago', function ($expression) {
            return "<?php echo time_ago($expression); ?>";
        });

        // Custom directive for blockchain icon
        Blade::directive('blockchainIcon', function ($expression) {
            return "<?php echo blockchain_icon($expression); ?>";
        });

        // Custom directive for checking wallet connection
        Blade::directive('wallet', function ($blockchain = null) {
            return "<?php if(auth()->check() && auth()->user()->wallets()->where('blockchain_id', $blockchain)->exists()): ?>";
        });

        Blade::directive('endwallet', function () {
            return "<?php endif; ?>";
        });
    }
}
