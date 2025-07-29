<?php
// app/View/Components/AirdropCard.php

namespace App\View\Components;

use App\Models\Airdrop;
use Illuminate\View\Component;

class AirdropCard extends Component
{
    public Airdrop $airdrop;
    public bool $showActions;
    public bool $isSubscribed;
    public bool $isFavorited;

    public function __construct(
        Airdrop $airdrop, 
        bool $showActions = true,
        bool $isSubscribed = false,
        bool $isFavorited = false
    ) {
        $this->airdrop = $airdrop;
        $this->showActions = $showActions;
        $this->isSubscribed = $isSubscribed;
        $this->isFavorited = $isFavorited;
    }

    public function render()
    {
        return view('components.airdrop-card');
    }

    public function getStatusBadgeClass()
    {
        return match($this->airdrop->status) {
            'active' => 'bg-green-500 text-white',
            'upcoming' => 'bg-blue-500 text-white',
            'ended' => 'bg-gray-500 text-white',
            'cancelled' => 'bg-red-500 text-white',
            default => 'bg-gray-400 text-white',
        };
    }

    public function getDaysRemaining()
    {
        if (!$this->airdrop->ends_at) return null;
        
        $days = now()->diffInDays($this->airdrop->ends_at, false);
        return $days > 0 ? $days : 0;
    }
}
