<?php
// app/Events/AirdropCreated.php

namespace App\Events;

use App\Models\Airdrop;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AirdropCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $airdrop;

    public function __construct(Airdrop $airdrop)
    {
        $this->airdrop = $airdrop;
    }
}
