<?php
// app/Events/AirdropStatusChanged.php

namespace App\Events;

use App\Models\Airdrop;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AirdropStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $airdrop;
    public $oldStatus;
    public $newStatus;

    public function __construct(Airdrop $airdrop, string $oldStatus, string $newStatus)
    {
        $this->airdrop = $airdrop;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
