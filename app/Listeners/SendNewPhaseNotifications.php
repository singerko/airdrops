<?php
// app/Listeners/SendNewPhaseNotifications.php

namespace App\Listeners;

use App\Events\NewPhaseAdded;
use App\Services\NotificationService;

class SendNewPhaseNotifications
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(NewPhaseAdded $event)
    {
        $this->notificationService->sendAirdropUpdate(
            $event->airdrop,
            'new_phase',
            ['phase' => $event->phase]
        );
    }
}
