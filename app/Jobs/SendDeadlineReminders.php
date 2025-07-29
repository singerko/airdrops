<?php
// app/Jobs/SendDeadlineReminders.php

namespace App\Jobs;

use App\Models\Airdrop;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDeadlineReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService)
    {
        // Find airdrops ending in 24 hours
        $endingSoon = Airdrop::where('status', 'active')
            ->whereBetween('ends_at', [now()->addHours(23), now()->addHours(25)])
            ->get();

        foreach ($endingSoon as $airdrop) {
            $notificationService->sendAirdropUpdate(
                $airdrop, 
                'deadline_reminder',
                ['hours_remaining' => 24]
            );
        }

        // Find airdrops ending in 7 days
        $endingThisWeek = Airdrop::where('status', 'active')
            ->whereBetween('ends_at', [now()->addDays(6), now()->addDays(8)])
            ->get();

        foreach ($endingThisWeek as $airdrop) {
            $notificationService->sendAirdropUpdate(
                $airdrop, 
                'airdrop_ending',
                ['days_remaining' => 7]
            );
        }
    }
}
