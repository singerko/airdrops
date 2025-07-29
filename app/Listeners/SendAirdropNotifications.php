<?php
// app/Listeners/SendAirdropNotifications.php

namespace App\Listeners;

use App\Events\AirdropCreated;
use App\Events\AirdropUpdated;
use App\Events\AirdropStatusChanged;
use App\Services\NotificationService;

class SendAirdropNotifications
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle($event)
    {
        $airdrop = $event->airdrop;
        
        if ($event instanceof AirdropCreated) {
            // Notify users about new airdrop on their preferred blockchains
            $this->notificationService->sendAirdropUpdate($airdrop, 'new_airdrop');
        } elseif ($event instanceof AirdropStatusChanged) {
            // Notify subscribers about status change
            $this->notificationService->sendAirdropUpdate($airdrop, 'status_change', [
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
            ]);
        } elseif ($event instanceof AirdropUpdated) {
            // Notify subscribers about updates
            $this->notificationService->sendAirdropUpdate($airdrop, 'airdrop_update', [
                'changes' => $event->changes,
            ]);
        }
    }
}
