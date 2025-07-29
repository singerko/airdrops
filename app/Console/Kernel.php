<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\UpdateAirdropStatusesCommand::class,
        Commands\SendWeeklyDigestCommand::class,
        Commands\SendDeadlineRemindersCommand::class,
        Commands\CleanupExpiredData::class,
        Commands\GenerateSitemap::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Update airdrop statuses every hour
        $schedule->command('airdrops:update-statuses')->hourly();
        
        // Send deadline reminders twice daily
        $schedule->command('notifications:deadline-reminders')->twiceDaily();
        
        // Send weekly digest every Sunday at 9 AM
        $schedule->command('notifications:weekly-digest')->weekly()->sundays()->at('09:00');
        
        // Clean up expired data daily at 2 AM
        $schedule->command('cleanup:expired-data')->dailyAt('02:00');
        
        // Generate sitemap daily at 3 AM
        $schedule->command('sitemap:generate')->dailyAt('03:00');
        
        // Clear cache weekly
        $schedule->command('cache:clear')->weekly();
        
        // Queue work (for production without supervisor)
        // $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
