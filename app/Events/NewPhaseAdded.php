<?php
// app/Events/NewPhaseAdded.php

namespace App\Events;

use App\Models\Airdrop;
use App\Models\AirdropPhase;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPhaseAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $airdrop;
    public $phase;

    public function __construct(Airdrop $airdrop, AirdropPhase $phase)
    {
        $this->airdrop = $airdrop;
        $this->phase = $phase;
    }
}
