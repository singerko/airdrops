<?php
// app/Events/AirdropUpdated.php

namespace App\Events;

use App\Models\Airdrop;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AirdropUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $airdrop;
    public $changes;

    public function __construct(Airdrop $airdrop, array $changes = [])
    {
        $this->airdrop = $airdrop;
        $this->changes = $changes;
    }
}
