<?php
// app/Jobs/SendWeeklyDigest.php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWeeklyDigest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(NotificationService $notificationService)
    {
        $users = User::whereJsonContains('notification_settings->weekly_digest', true)
            ->whereNotNull('email')
            ->chunk(100, function ($users) use ($notificationService) {
                foreach ($users as $user) {
                    $notificationService->sendWeeklyDigest($user);
                }
            });
    }
}
