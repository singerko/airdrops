<?php
// app/Observers/AirdropObserver.php

namespace App\Observers;

use App\Models\Airdrop;
use App\Events\AirdropCreated;
use App\Events\AirdropUpdated;
use App\Events\AirdropStatusChanged;

class AirdropObserver
{
    public function created(Airdrop $airdrop)
    {
        if ($airdrop->status !== 'draft') {
            event(new AirdropCreated($airdrop));
        }
    }

    public function updated(Airdrop $airdrop)
    {
        $changes = $airdrop->getChanges();
        
        if (isset($changes['status'])) {
            event(new AirdropStatusChanged(
                $airdrop,
                $airdrop->getOriginal('status'),
                $airdrop->status
            ));
        } else {
            event(new AirdropUpdated($airdrop, $changes));
        }
    }

    public function deleting(Airdrop $airdrop)
    {
        // Clean up related data
        $airdrop->phases()->delete();
        $airdrop->translations()->delete();
        $airdrop->subscriptions()->delete();
        $airdrop->favorites()->delete();
    }
}
