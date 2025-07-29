<?php
// app/Jobs/UpdateAirdropStatuses.php

namespace App\Jobs;

use App\Models\Airdrop;
use App\Events\AirdropStatusChanged;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAirdropStatuses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Update upcoming airdrops to active
        $upcomingToActive = Airdrop::where('status', 'upcoming')
            ->where('starts_at', '<=', now())
            ->get();

        foreach ($upcomingToActive as $airdrop) {
            $oldStatus = $airdrop->status;
            $airdrop->update(['status' => 'active']);
            event(new AirdropStatusChanged($airdrop, $oldStatus, 'active'));
        }

        // Update active airdrops to ended
        $activeToEnded = Airdrop::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->get();

        foreach ($activeToEnded as $airdrop) {
            $oldStatus = $airdrop->status;
            $airdrop->update(['status' => 'ended']);
            event(new AirdropStatusChanged($airdrop, $oldStatus, 'ended'));
        }
    }
}
