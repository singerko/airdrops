<?php
// app/View/Components/BlockchainIcon.php

namespace App\View\Components;

use App\Models\Blockchain;
use Illuminate\View\Component;

class BlockchainIcon extends Component
{
    public Blockchain $blockchain;
    public string $size;
    public bool $showName;

    public function __construct(Blockchain $blockchain, string $size = 'md', bool $showName = false)
    {
        $this->blockchain = $blockchain;
        $this->size = $size;
        $this->showName = $showName;
    }

    public function render()
    {
        return view('components.blockchain-icon')->with('component', $this);
    }

    public function getIconClass()
    {
        return match($this->size) {
            'xs' => 'w-4 h-4',
            'sm' => 'w-6 h-6',
            'md' => 'w-8 h-8',
            'lg' => 'w-12 h-12',
            'xl' => 'w-16 h-16',
            default => 'w-8 h-8',
        };
    }

    public function getIcon()
    {
        return match($this->blockchain->slug) {
            'ethereum' => '⟠',
            'solana' => '◎',
            'cosmos' => '⚛',
            'polygon' => '⬟',
            'bsc' => '🔶',
            'arbitrum' => '🔷',
            default => '🔗',
        };
    }
}
